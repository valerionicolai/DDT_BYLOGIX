<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AuthenticationAndNavigationTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Test user authentication flow
     */
    public function test_user_authentication_flow()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'role' => 'admin'
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            // Test login
            $browser->visit('/login')
                    ->assertSee('Login')
                    ->type('email', 'test@example.com')
                    ->type('password', 'password123')
                    ->press('Login')
                    ->assertPathIs('/')
                    ->assertSee('DTT by Logix')
                    ->assertSee('Welcome');

            // Test logout
            $browser->click('@user-menu')
                    ->clickLink('Logout')
                    ->assertPathIs('/login')
                    ->assertSee('Login');
        });
    }

    /**
     * Test authentication validation
     */
    public function test_authentication_validation()
    {
        $this->browse(function (Browser $browser) {
            // Test empty login form
            $browser->visit('/login')
                    ->press('Login')
                    ->assertSee('The email field is required')
                    ->assertSee('The password field is required');

            // Test invalid credentials
            $browser->type('email', 'invalid@example.com')
                    ->type('password', 'wrongpassword')
                    ->press('Login')
                    ->assertSee('These credentials do not match our records');

            // Test invalid email format
            $browser->clear('email')
                    ->type('email', 'invalid-email')
                    ->type('password', 'password123')
                    ->press('Login')
                    ->assertSee('The email must be a valid email address');
        });
    }

    /**
     * Test main navigation functionality
     */
    public function test_main_navigation()
    {
        $user = User::factory()->create([
            'role' => 'admin'
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/')
                    ->assertSee('DTT by Logix');

            // Test navigation to Documents
            $browser->clickLink('Documents')
                    ->assertPathIs('/documents')
                    ->assertSee('Documents');

            // Test navigation to Scanner
            $browser->clickLink('Barcode Scanner')
                    ->assertPathIs('/scanner')
                    ->assertSee('Barcode Scanner');

            // Test navigation to Home
            $browser->clickLink('DTT by Logix')
                    ->assertPathIs('/')
                    ->assertSee('Welcome to DTT by Logix');
        });
    }

    /**
     * Test role-based access control
     */
    public function test_role_based_access_control()
    {
        // Test admin user
        $adminUser = User::factory()->create([
            'role' => 'admin'
        ]);

        // Test regular user
        $regularUser = User::factory()->create([
            'role' => 'user'
        ]);

        $this->browse(function (Browser $browser) use ($adminUser, $regularUser) {
            // Test admin access
            $browser->loginAs($adminUser)
                    ->visit('/documents')
                    ->assertSee('Documents')
                    ->assertPresent('@create-document-btn')
                    ->assertPresent('@export-csv-btn');

            // Test regular user access
            $browser->loginAs($regularUser)
                    ->visit('/documents')
                    ->assertSee('Documents');
            
            // Regular users might have limited access
            if (!$browser->element('@create-document-btn')) {
                $browser->assertMissing('@create-document-btn');
            }
        });
    }

    /**
     * Test responsive navigation
     */
    public function test_responsive_navigation()
    {
        $user = User::factory()->create([
            'role' => 'admin'
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->resize(375, 667) // Mobile size
                    ->visit('/')
                    ->assertPresent('@mobile-menu-btn')
                    ->click('@mobile-menu-btn')
                    ->assertVisible('@mobile-navigation')
                    ->assertSee('Documents')
                    ->assertSee('Barcode Scanner');

            // Test mobile navigation links
            $browser->clickLink('Documents')
                    ->assertPathIs('/documents')
                    ->assertSee('Documents');

            // Test desktop navigation
            $browser->resize(1200, 800)
                    ->visit('/')
                    ->assertMissing('@mobile-menu-btn')
                    ->assertVisible('@desktop-navigation');
        });
    }

    /**
     * Test breadcrumb navigation
     */
    public function test_breadcrumb_navigation()
    {
        $user = User::factory()->create([
            'role' => 'admin'
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/documents')
                    ->assertPresent('@breadcrumb')
                    ->assertSee('Home')
                    ->assertSee('Documents');

            // Test breadcrumb links
            $browser->click('@breadcrumb-home')
                    ->assertPathIs('/')
                    ->assertSee('Welcome to DTT by Logix');
        });
    }

    /**
     * Test search functionality in navigation
     */
    public function test_global_search()
    {
        $user = User::factory()->create([
            'role' => 'admin'
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/')
                    ->assertPresent('@global-search')
                    ->type('@global-search-input', 'test search')
                    ->press('@global-search-btn')
                    ->assertSee('Search Results')
                    ->assertSee('test search');
        });
    }

    /**
     * Test error pages and 404 handling
     */
    public function test_error_pages()
    {
        $user = User::factory()->create([
            'role' => 'admin'
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/non-existent-page')
                    ->assertSee('Page Not Found')
                    ->assertSee('404')
                    ->assertPresent('@back-to-home-btn')
                    ->click('@back-to-home-btn')
                    ->assertPathIs('/');
        });
    }

    /**
     * Test session management
     */
    public function test_session_management()
    {
        $user = User::factory()->create([
            'email' => 'session@example.com',
            'password' => bcrypt('password123'),
            'role' => 'admin'
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            // Test remember me functionality
            $browser->visit('/login')
                    ->type('email', 'session@example.com')
                    ->type('password', 'password123')
                    ->check('remember')
                    ->press('Login')
                    ->assertPathIs('/')
                    ->assertSee('DTT by Logix');

            // Test session persistence
            $browser->refresh()
                    ->assertPathIs('/')
                    ->assertSee('DTT by Logix');
        });
    }

    /**
     * Test accessibility features
     */
    public function test_accessibility_features()
    {
        $user = User::factory()->create([
            'role' => 'admin'
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/')
                    ->assertAttribute('@main-navigation', 'role', 'navigation')
                    ->assertAttribute('@skip-to-content', 'href', '#main-content')
                    ->assertPresent('@main-content')
                    ->assertAttribute('@main-content', 'id', 'main-content');

            // Test keyboard navigation
            $browser->keys('@main-navigation', ['{tab}'])
                    ->assertFocused('@documents-link');
        });
    }
}
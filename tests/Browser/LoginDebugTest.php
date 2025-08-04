<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoginDebugTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Debug authentication state after loginAs
     */
    public function test_debug_authentication_state()
    {
        $this->browse(function (Browser $browser) {
            // Create admin user
            $admin = \App\Models\User::factory()->create([
                'name' => 'Admin User',
                'email' => 'admin@dttbylogix.com',
                'password' => bcrypt('password'),
                'role' => 'admin'
            ]);
            
            echo "\n=== DEBUGGING AUTHENTICATION STATE ===\n";
            
            // Step 1: Login using Laravel's loginAs
            $browser->loginAs($admin);
            echo "✓ Laravel loginAs completed\n";
            
            // Step 2: Visit dashboard (correct route is /)
            $browser->visit('/');
            echo "✓ Visited dashboard (/) after loginAs\n";
            
            // Step 3: Check current URL and title
            $currentUrl = $browser->driver->getCurrentURL();
            $pageTitle = $browser->driver->getTitle();
            echo "Current URL: $currentUrl\n";
            echo "Page Title: $pageTitle\n";
            
            // Step 4: Wait for page to load
            $browser->pause(3000);
            
            // Step 5: Check if Vue.js is loaded
            $vueLoaded = $browser->driver->executeScript("return typeof window.Vue !== 'undefined' || typeof window.__VUE__ !== 'undefined';");
            echo "Vue.js loaded: " . ($vueLoaded ? 'Yes' : 'No') . "\n";
            
            // Step 6: Check if Axios is available
            $axiosAvailable = $browser->driver->executeScript("return typeof window.axios !== 'undefined';");
            echo "Axios available: " . ($axiosAvailable ? 'Yes' : 'No') . "\n";
            
            // Step 7: Try to fetch user data manually
            if ($axiosAvailable) {
                $userDataResult = $browser->driver->executeScript("
                    return new Promise((resolve) => {
                        window.axios.get('/api/auth/user')
                            .then(response => resolve({success: true, status: response.status, data: response.data}))
                            .catch(error => resolve({success: false, status: error.response?.status || 'unknown', message: error.message}));
                    });
                ");
                echo "User data fetch result: " . json_encode($userDataResult) . "\n";
            }
            
            // Step 8: Check page content
            $pageSource = $browser->driver->getPageSource();
            $hasLogin = strpos($pageSource, 'Accedi al tuo account') !== false;
            $hasDashboard = strpos($pageSource, 'Dashboard') !== false;
            $hasLogout = strpos($pageSource, 'Logout') !== false;
            
            echo "Page contains 'Accedi al tuo account': " . ($hasLogin ? 'Yes' : 'No') . "\n";
            echo "Page contains 'Dashboard': " . ($hasDashboard ? 'Yes' : 'No') . "\n";
            echo "Page contains 'Logout': " . ($hasLogout ? 'Yes' : 'No') . "\n";
            
            // Step 9: Show body text preview
            $bodyText = $browser->driver->executeScript("return document.body.innerText.substring(0, 500);");
            echo "Body text preview: " . substr($bodyText, 0, 200) . "...\n";
            
            echo "=== END DEBUG ===\n";
            
            // Always pass for debugging
            $this->assertTrue(true);
        });
    }
}
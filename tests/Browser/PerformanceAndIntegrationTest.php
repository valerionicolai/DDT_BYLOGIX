<?php

namespace Tests\Browser;

use App\Models\Document;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PerformanceAndIntegrationTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Test application performance with large datasets
     */
    public function test_performance_with_large_dataset()
    {
        $user = User::factory()->create([
            'role' => 'admin'
        ]);

        // Create a large number of documents
        Document::factory()->count(100)->create();

        $this->browse(function (Browser $browser) use ($user) {
            $startTime = microtime(true);
            
            $browser->loginAs($user)
                    ->visit('/documents')
                    ->assertSee('Documents');
            
            $loadTime = microtime(true) - $startTime;
            
            // Assert page loads within reasonable time (5 seconds)
            $this->assertLessThan(5, $loadTime, 'Page load time exceeded 5 seconds');
            
            // Test pagination performance
            $browser->assertPresent('@pagination')
                    ->click('@next-page-btn')
                    ->pause(1000)
                    ->assertSee('Page 2');
        });
    }

    /**
     * Test CSV export with large dataset
     */
    public function test_csv_export_performance()
    {
        $user = User::factory()->create([
            'role' => 'admin'
        ]);

        // Create documents for export
        Document::factory()->count(50)->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/documents')
                    ->assertSee('Documents');

            $startTime = microtime(true);
            
            $browser->click('@export-csv-btn')
                    ->pause(3000); // Wait for download
            
            $exportTime = microtime(true) - $startTime;
            
            // Assert export completes within reasonable time (10 seconds)
            $this->assertLessThan(10, $exportTime, 'CSV export time exceeded 10 seconds');
        });
    }

    /**
     * Test complete user workflow integration
     */
    public function test_complete_user_workflow()
    {
        $user = User::factory()->create([
            'email' => 'workflow@example.com',
            'password' => bcrypt('password123'),
            'role' => 'admin'
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            // Complete workflow: Login -> Create Document -> Scan -> Edit -> Export -> Logout
            
            // Step 1: Login
            $browser->visit('/login')
                    ->type('email', 'workflow@example.com')
                    ->type('password', 'password123')
                    ->press('Login')
                    ->assertPathIs('/')
                    ->assertSee('DTT by Logix');

            // Step 2: Create a document
            $browser->clickLink('Documents')
                    ->assertPathIs('/documents')
                    ->click('@create-document-btn')
                    ->assertPathIs('/documents/create')
                    ->type('title', 'Workflow Test Document')
                    ->type('description', 'Document created during workflow test')
                    ->select('category', 'Contract')
                    ->type('supplier', 'Workflow Supplier')
                    ->press('Save Document')
                    ->assertPathIs('/documents')
                    ->assertSee('Workflow Test Document');

            // Step 3: Get the document and its barcode
            $document = Document::where('title', 'Workflow Test Document')->first();
            $this->assertNotNull($document);

            // Step 4: Test barcode scanning
            $browser->clickLink('Barcode Scanner')
                    ->assertPathIs('/scanner')
                    ->type('@manual-barcode-input', $document->barcode)
                    ->press('@search-barcode-btn')
                    ->pause(2000)
                    ->assertPathIs('/documents/' . $document->id)
                    ->assertSee('Workflow Test Document');

            // Step 5: Edit the document
            $browser->click('@edit-document-btn')
                    ->clear('description')
                    ->type('description', 'Updated during workflow test')
                    ->press('Update Document')
                    ->assertSee('Updated during workflow test');

            // Step 6: Export CSV
            $browser->visit('/documents')
                    ->click('@export-csv-btn')
                    ->pause(2000);

            // Step 7: Logout
            $browser->click('@user-menu')
                    ->clickLink('Logout')
                    ->assertPathIs('/login')
                    ->assertSee('Login');
        });
    }

    /**
     * Test error recovery and resilience
     */
    public function test_error_recovery()
    {
        $user = User::factory()->create([
            'role' => 'admin'
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/documents');

            // Test network error simulation (if possible)
            // This would require additional setup for network interception
            
            // Test form validation recovery
            $browser->click('@create-document-btn')
                    ->press('Save Document')
                    ->assertSee('The title field is required')
                    ->type('title', 'Recovery Test')
                    ->select('category', 'Contract')
                    ->press('Save Document')
                    ->assertPathIs('/documents')
                    ->assertSee('Recovery Test');

            // Test navigation after error
            $browser->visit('/non-existent-page')
                    ->assertSee('Page Not Found')
                    ->clickLink('Documents')
                    ->assertPathIs('/documents')
                    ->assertSee('Documents');
        });
    }

    /**
     * Test concurrent user actions
     */
    public function test_concurrent_operations()
    {
        $user1 = User::factory()->create([
            'email' => 'user1@example.com',
            'role' => 'admin'
        ]);

        $user2 = User::factory()->create([
            'email' => 'user2@example.com',
            'role' => 'admin'
        ]);

        $document = Document::factory()->create([
            'title' => 'Concurrent Test Document'
        ]);

        $this->browse(function (Browser $browser1, Browser $browser2) use ($user1, $user2, $document) {
            // User 1 views document
            $browser1->loginAs($user1)
                     ->visit('/documents/' . $document->id)
                     ->assertSee('Concurrent Test Document');

            // User 2 edits the same document
            $browser2->loginAs($user2)
                     ->visit('/documents/' . $document->id)
                     ->click('@edit-document-btn')
                     ->clear('title')
                     ->type('title', 'Updated by User 2')
                     ->press('Update Document')
                     ->assertSee('Updated by User 2');

            // User 1 refreshes and sees the update
            $browser1->refresh()
                     ->assertSee('Updated by User 2');
        });
    }

    /**
     * Test data consistency across operations
     */
    public function test_data_consistency()
    {
        $user = User::factory()->create([
            'role' => 'admin'
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/documents');

            // Create document and verify consistency
            $browser->click('@create-document-btn')
                    ->type('title', 'Consistency Test')
                    ->type('description', 'Testing data consistency')
                    ->select('category', 'Contract')
                    ->type('supplier', 'Test Supplier')
                    ->press('Save Document')
                    ->assertSee('Consistency Test');

            // Verify document appears in list
            $browser->visit('/documents')
                    ->assertSee('Consistency Test')
                    ->assertSee('Contract')
                    ->assertSee('Test Supplier');

            // Verify document details are consistent
            $browser->clickLink('Consistency Test')
                    ->assertSee('Consistency Test')
                    ->assertSee('Testing data consistency')
                    ->assertSee('Contract')
                    ->assertSee('Test Supplier')
                    ->assertPresent('@barcode-display');

            // Verify barcode search finds the document
            $document = Document::where('title', 'Consistency Test')->first();
            $browser->visit('/scanner')
                    ->type('@manual-barcode-input', $document->barcode)
                    ->press('@search-barcode-btn')
                    ->pause(2000)
                    ->assertPathIs('/documents/' . $document->id);
        });
    }

    /**
     * Test browser compatibility features
     */
    public function test_browser_compatibility()
    {
        $user = User::factory()->create([
            'role' => 'admin'
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/')
                    ->assertSee('DTT by Logix');

            // Test JavaScript functionality
            $browser->assertPresent('@interactive-elements')
                    ->click('@dropdown-toggle')
                    ->assertVisible('@dropdown-menu');

            // Test CSS loading
            $browser->assertPresent('@styled-elements')
                    ->assertVisible('@navigation-bar');

            // Test responsive design
            $browser->resize(768, 1024) // Tablet size
                    ->assertVisible('@main-content')
                    ->resize(1920, 1080) // Desktop size
                    ->assertVisible('@main-content');
        });
    }

    /**
     * Test memory usage and cleanup
     */
    public function test_memory_usage()
    {
        $user = User::factory()->create([
            'role' => 'admin'
        ]);

        // Create documents to test memory usage
        Document::factory()->count(20)->create();

        $this->browse(function (Browser $browser) use ($user) {
            $initialMemory = memory_get_usage();

            $browser->loginAs($user)
                    ->visit('/documents')
                    ->assertSee('Documents');

            // Navigate through multiple pages
            for ($i = 0; $i < 5; $i++) {
                $browser->visit('/documents')
                        ->visit('/scanner')
                        ->visit('/');
            }

            $finalMemory = memory_get_usage();
            $memoryIncrease = $finalMemory - $initialMemory;

            // Assert memory increase is reasonable (less than 50MB)
            $this->assertLessThan(50 * 1024 * 1024, $memoryIncrease, 'Memory usage increased too much');
        });
    }
}
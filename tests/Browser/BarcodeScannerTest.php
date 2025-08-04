<?php

namespace Tests\Browser;

use App\Models\Document;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class BarcodeScannerTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Test barcode scanner page accessibility
     */
    public function test_barcode_scanner_page_access()
    {
        $user = User::factory()->create([
            'role' => 'admin'
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit('/')
                    ->assertSee('DTT by Logix')
                    ->clickLink('Barcode Scanner')
                    ->assertPathIs('/scanner')
                    ->assertSee('Barcode Scanner')
                    ->assertSee('Scan a barcode to find documents')
                    ->assertPresent('@scanner-component')
                    ->assertPresent('@manual-input');
        });
    }

    /**
     * Test manual barcode input functionality
     */
    public function test_manual_barcode_input()
    {
        $user = User::factory()->create([
            'role' => 'admin'
        ]);

        // Create a document with a known barcode
        $document = Document::factory()->create([
            'title' => 'Test Document for Barcode',
            'barcode' => 'TEST123456',
            'category' => 'Contract'
        ]);

        $this->browse(function (Browser $browser) use ($user, $document) {
            $browser->visit('/scanner')
                    ->type('@manual-barcode-input', 'TEST123456')
                    ->press('@search-barcode-btn')
                    ->pause(2000) // Wait for search and redirect
                    ->assertPathIs('/documents/' . $document->id)
                    ->assertSee('Test Document for Barcode')
                    ->assertSee('TEST123456');
        });
    }

    /**
     * Test barcode search with no results
     */
    public function test_barcode_search_no_results()
    {
        $user = User::factory()->create([
            'role' => 'admin'
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit('/scanner')
                    ->type('@manual-barcode-input', 'NONEXISTENT123')
                    ->press('@search-barcode-btn')
                    ->pause(1000)
                    ->assertSee('No documents found')
                    ->assertSee('Create New Document')
                    ->click('@create-new-document-btn')
                    ->assertPathIs('/documents/create')
                    ->assertInputValue('barcode', 'NONEXISTENT123');
        });
    }

    /**
     * Test barcode search with multiple results
     */
    public function test_barcode_search_multiple_results()
    {
        $user = User::factory()->create([
            'role' => 'admin'
        ]);

        // Create multiple documents with similar barcodes
        $document1 = Document::factory()->create([
            'title' => 'Document One',
            'barcode' => 'MULTI123',
            'category' => 'Contract'
        ]);

        $document2 = Document::factory()->create([
            'title' => 'Document Two',
            'barcode' => 'MULTI124',
            'category' => 'Invoice'
        ]);

        $this->browse(function (Browser $browser) use ($user, $document1, $document2) {
            $browser->visit('/scanner')
                    ->type('@manual-barcode-input', 'MULTI')
                    ->press('@search-barcode-btn')
                    ->pause(1000)
                    ->assertSee('Multiple documents found')
                    ->assertSee('Document One')
                    ->assertSee('Document Two')
                    ->assertSee('MULTI123')
                    ->assertSee('MULTI124');

            // Test clicking on a specific document
            $browser->clickLink('Document One')
                    ->assertPathIs('/documents/' . $document1->id)
                    ->assertSee('Document One');
        });
    }

    /**
     * Test scanner component initialization
     */
    public function test_scanner_component_initialization()
    {
        $user = User::factory()->create([
            'role' => 'admin'
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit('/scanner')
                    ->assertPresent('@scanner-component')
                    ->assertPresent('@start-scanner-btn')
                    ->assertPresent('@stop-scanner-btn')
                    ->assertPresent('@scanner-status');

            // Test scanner controls
            $browser->click('@start-scanner-btn')
                    ->pause(1000)
                    ->assertSee('Scanner started')
                    ->click('@stop-scanner-btn')
                    ->pause(1000)
                    ->assertSee('Scanner stopped');
        });
    }

    /**
     * Test scanner error handling
     */
    public function test_scanner_error_handling()
    {
        $user = User::factory()->create([
            'role' => 'admin'
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit('/scanner')
                    ->assertPresent('@scanner-component');

            // Test empty barcode input
            $browser->clear('@manual-barcode-input')
                    ->press('@search-barcode-btn')
                    ->assertSee('Please enter a barcode');

            // Test invalid barcode format (if validation exists)
            $browser->type('@manual-barcode-input', '   ')
                    ->press('@search-barcode-btn')
                    ->assertSee('Please enter a valid barcode');
        });
    }

    /**
     * Test scanner navigation and back functionality
     */
    public function test_scanner_navigation()
    {
        $user = User::factory()->create([
            'role' => 'admin'
        ]);

        $document = Document::factory()->create([
            'title' => 'Navigation Test Document',
            'barcode' => 'NAV123456',
            'category' => 'Contract'
        ]);

        $this->browse(function (Browser $browser) use ($user, $document) {
            $browser->visit('/scanner')
                    ->type('@manual-barcode-input', 'NAV123456')
                    ->press('@search-barcode-btn')
                    ->pause(2000)
                    ->assertPathIs('/documents/' . $document->id);

            // Test back to scanner
            $browser->back()
                    ->assertPathIs('/scanner')
                    ->assertInputValue('@manual-barcode-input', '');
        });
    }

    /**
     * Test scanner accessibility features
     */
    public function test_scanner_accessibility()
    {
        $user = User::factory()->create([
            'role' => 'admin'
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit('/scanner')
                    ->assertAttribute('@manual-barcode-input', 'aria-label', 'Barcode input')
                    ->assertAttribute('@search-barcode-btn', 'aria-label', 'Search barcode')
                    ->assertPresent('@scanner-instructions')
                    ->assertSee('Use the camera to scan a barcode or enter it manually');
        });
    }

    /**
     * Test scanner on mobile devices
     */
    public function test_scanner_mobile_responsive()
    {
        $user = User::factory()->create([
            'role' => 'admin'
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->resize(375, 667) // iPhone SE size
                    ->visit('/scanner')
                    ->assertPresent('@scanner-component')
                    ->assertVisible('@manual-barcode-input')
                    ->assertVisible('@search-barcode-btn');

            // Test mobile-specific scanner features
            $browser->assertPresent('@mobile-scanner-controls')
                    ->assertVisible('@mobile-scanner-controls');
        });
    }
}
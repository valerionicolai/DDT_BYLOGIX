<?php

namespace Tests\Browser;

use App\Models\Document;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AccessibilityAndUsabilityTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Test keyboard navigation accessibility
     */
    public function test_keyboard_navigation()
    {
        $user = User::factory()->create([
            'role' => 'admin'
        ]);

        Document::factory()->count(3)->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/documents')
                    ->assertSee('Documents');

            // Test tab navigation
            $browser->keys('body', ['{tab}'])
                    ->assertFocused('@first-focusable-element');

            // Test navigation through form elements
            $browser->click('@create-document-btn')
                    ->keys('body', ['{tab}'])
                    ->assertFocused('input[name="title"]')
                    ->keys('body', ['{tab}'])
                    ->assertFocused('textarea[name="description"]')
                    ->keys('body', ['{tab}'])
                    ->assertFocused('select[name="category"]');

            // Test escape key functionality
            $browser->keys('body', ['{escape}'])
                    ->assertPathIs('/documents');
        });
    }

    /**
     * Test screen reader compatibility
     */
    public function test_screen_reader_compatibility()
    {
        $user = User::factory()->create([
            'role' => 'admin'
        ]);

        $document = Document::factory()->create([
            'title' => 'Accessibility Test Document'
        ]);

        $this->browse(function (Browser $browser) use ($user, $document) {
            $browser->loginAs($user)
                    ->visit('/documents')
                    ->assertSee('Documents');

            // Test ARIA labels and roles
            $browser->assertAttribute('@main-content', 'role', 'main')
                    ->assertAttribute('@navigation-menu', 'role', 'navigation')
                    ->assertAttribute('@document-list', 'role', 'list');

            // Test alt text for images/icons
            $browser->assertAttribute('@logo-image', 'alt', 'DTT by Logix Logo')
                    ->assertAttribute('@search-icon', 'aria-label', 'Search');

            // Test form labels
            $browser->click('@create-document-btn')
                    ->assertAttribute('input[name="title"]', 'aria-label', 'Document Title')
                    ->assertAttribute('textarea[name="description"]', 'aria-label', 'Document Description')
                    ->assertAttribute('select[name="category"]', 'aria-label', 'Document Category');

            // Test heading hierarchy
            $browser->visit('/documents/' . $document->id)
                    ->assertPresent('h1')
                    ->assertPresent('h2')
                    ->assertSeeIn('h1', 'Accessibility Test Document');
        });
    }

    /**
     * Test color contrast and visual accessibility
     */
    public function test_visual_accessibility()
    {
        $user = User::factory()->create([
            'role' => 'admin'
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/')
                    ->assertSee('DTT by Logix');

            // Test high contrast mode compatibility
            $browser->script('document.body.style.filter = "contrast(150%)";');
            $browser->pause(1000)
                    ->assertVisible('@main-content')
                    ->assertVisible('@navigation-menu');

            // Reset contrast
            $browser->script('document.body.style.filter = "none";');

            // Test text scaling
            $browser->script('document.body.style.fontSize = "150%";');
            $browser->pause(1000)
                    ->assertVisible('@main-content')
                    ->assertVisible('@navigation-menu');

            // Reset font size
            $browser->script('document.body.style.fontSize = "100%";');
        });
    }

    /**
     * Test mobile usability
     */
    public function test_mobile_usability()
    {
        $user = User::factory()->create([
            'role' => 'admin'
        ]);

        Document::factory()->count(5)->create();

        $this->browse(function (Browser $browser) use ($user) {
            // Test mobile viewport
            $browser->resize(375, 667) // iPhone SE size
                    ->loginAs($user)
                    ->visit('/documents')
                    ->assertSee('Documents');

            // Test mobile navigation
            $browser->assertPresent('@mobile-menu-toggle')
                    ->click('@mobile-menu-toggle')
                    ->assertVisible('@mobile-navigation-menu')
                    ->click('@mobile-menu-close')
                    ->assertNotVisible('@mobile-navigation-menu');

            // Test touch-friendly buttons
            $browser->assertPresent('@create-document-btn')
                    ->click('@create-document-btn')
                    ->assertPathIs('/documents/create');

            // Test form usability on mobile
            $browser->type('title', 'Mobile Test Document')
                    ->type('description', 'Testing mobile form usability')
                    ->select('category', 'Contract')
                    ->press('Save Document')
                    ->assertPathIs('/documents')
                    ->assertSee('Mobile Test Document');

            // Test horizontal scrolling
            $browser->assertScript('return document.body.scrollWidth <= window.innerWidth;');

            // Test tablet viewport
            $browser->resize(768, 1024) // iPad size
                    ->visit('/documents')
                    ->assertVisible('@main-content')
                    ->assertVisible('@sidebar');
        });
    }

    /**
     * Test form validation and error messages
     */
    public function test_form_validation_usability()
    {
        $user = User::factory()->create([
            'role' => 'admin'
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/documents/create')
                    ->assertSee('Create Document');

            // Test required field validation
            $browser->press('Save Document')
                    ->assertSee('The title field is required')
                    ->assertAttribute('input[name="title"]', 'aria-invalid', 'true')
                    ->assertPresent('@title-error-message');

            // Test field correction
            $browser->type('title', 'Validation Test')
                    ->assertAttribute('input[name="title"]', 'aria-invalid', 'false')
                    ->assertNotPresent('@title-error-message');

            // Test successful submission
            $browser->select('category', 'Contract')
                    ->press('Save Document')
                    ->assertPathIs('/documents')
                    ->assertSee('Document created successfully');
        });
    }

    /**
     * Test search and filter usability
     */
    public function test_search_filter_usability()
    {
        $user = User::factory()->create([
            'role' => 'admin'
        ]);

        Document::factory()->create(['title' => 'Contract Document', 'category' => 'Contract']);
        Document::factory()->create(['title' => 'Invoice Document', 'category' => 'Invoice']);
        Document::factory()->create(['title' => 'Report Document', 'category' => 'Report']);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/documents')
                    ->assertSee('Documents');

            // Test search functionality
            $browser->type('@search-input', 'Contract')
                    ->pause(1000)
                    ->assertSee('Contract Document')
                    ->assertDontSee('Invoice Document');

            // Test search clear
            $browser->clear('@search-input')
                    ->pause(1000)
                    ->assertSee('Contract Document')
                    ->assertSee('Invoice Document')
                    ->assertSee('Report Document');

            // Test category filter
            $browser->select('@category-filter', 'Invoice')
                    ->pause(1000)
                    ->assertSee('Invoice Document')
                    ->assertDontSee('Contract Document');

            // Test filter reset
            $browser->select('@category-filter', '')
                    ->pause(1000)
                    ->assertSee('Contract Document')
                    ->assertSee('Invoice Document')
                    ->assertSee('Report Document');

            // Test combined search and filter
            $browser->type('@search-input', 'Document')
                    ->select('@category-filter', 'Contract')
                    ->pause(1000)
                    ->assertSee('Contract Document')
                    ->assertDontSee('Invoice Document');
        });
    }

    /**
     * Test barcode scanner usability
     */
    public function test_barcode_scanner_usability()
    {
        $user = User::factory()->create([
            'role' => 'admin'
        ]);

        $document = Document::factory()->create([
            'title' => 'Scanner Test Document'
        ]);

        $this->browse(function (Browser $browser) use ($user, $document) {
            $browser->loginAs($user)
                    ->visit('/scanner')
                    ->assertSee('Barcode Scanner');

            // Test manual input usability
            $browser->assertPresent('@manual-barcode-input')
                    ->assertAttribute('@manual-barcode-input', 'placeholder', 'Enter barcode manually')
                    ->type('@manual-barcode-input', $document->barcode)
                    ->press('@search-barcode-btn')
                    ->pause(2000)
                    ->assertPathIs('/documents/' . $document->id);

            // Test invalid barcode handling
            $browser->visit('/scanner')
                    ->type('@manual-barcode-input', 'INVALID_BARCODE')
                    ->press('@search-barcode-btn')
                    ->pause(2000)
                    ->assertSee('No document found')
                    ->assertPathIs('/scanner');

            // Test barcode input validation
            $browser->clear('@manual-barcode-input')
                    ->press('@search-barcode-btn')
                    ->assertSee('Please enter a barcode');
        });
    }

    /**
     * Test loading states and feedback
     */
    public function test_loading_states_feedback()
    {
        $user = User::factory()->create([
            'role' => 'admin'
        ]);

        Document::factory()->count(10)->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/documents')
                    ->assertSee('Documents');

            // Test loading state during navigation
            $browser->click('@create-document-btn')
                    ->assertPresent('@loading-indicator')
                    ->waitFor('@document-form', 5)
                    ->assertNotPresent('@loading-indicator');

            // Test form submission feedback
            $browser->type('title', 'Loading Test Document')
                    ->select('category', 'Contract')
                    ->press('Save Document')
                    ->assertPresent('@saving-indicator')
                    ->waitForLocation('/documents', 10)
                    ->assertSee('Document created successfully');

            // Test search loading feedback
            $browser->type('@search-input', 'Loading')
                    ->assertPresent('@search-loading')
                    ->pause(1000)
                    ->assertNotPresent('@search-loading');
        });
    }

    /**
     * Test error handling and recovery
     */
    public function test_error_handling_usability()
    {
        $user = User::factory()->create([
            'role' => 'admin'
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/documents')
                    ->assertSee('Documents');

            // Test 404 error handling
            $browser->visit('/documents/999999')
                    ->assertSee('Document not found')
                    ->assertPresent('@back-to-documents-btn')
                    ->click('@back-to-documents-btn')
                    ->assertPathIs('/documents');

            // Test form error recovery
            $browser->click('@create-document-btn')
                    ->press('Save Document')
                    ->assertSee('The title field is required')
                    ->type('title', 'Error Recovery Test')
                    ->select('category', 'Contract')
                    ->press('Save Document')
                    ->assertPathIs('/documents')
                    ->assertSee('Error Recovery Test');

            // Test network error simulation (if possible)
            // This would require additional setup for network interception
        });
    }

    /**
     * Test internationalization and localization
     */
    public function test_internationalization()
    {
        $user = User::factory()->create([
            'role' => 'admin'
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/')
                    ->assertSee('DTT by Logix');

            // Test Italian language (if implemented)
            // This would require language switching functionality
            
            // Test date formatting
            $browser->visit('/documents')
                    ->assertPresent('@date-display')
                    ->assertSeeIn('@date-display', date('d/m/Y')); // Italian date format

            // Test number formatting
            $document = Document::factory()->create(['file_size' => 1024]);
            $browser->visit('/documents/' . $document->id)
                    ->assertSee('1 KB'); // Localized file size
        });
    }
}
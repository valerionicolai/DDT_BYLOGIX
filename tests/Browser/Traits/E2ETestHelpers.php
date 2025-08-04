<?php

namespace Tests\Browser\Traits;

use App\Models\Document;
use App\Models\User;
use Laravel\Dusk\Browser;

trait E2ETestHelpers
{
    /**
     * Create a test user with specified role
     */
    protected function createTestUser(string $role = 'admin', array $attributes = []): User
    {
        $defaultAttributes = [
            'email' => $role . '@dttbylogix.test',
            'password' => bcrypt('password123'),
            'role' => $role,
            'name' => ucfirst($role) . ' User',
        ];

        return User::factory()->create(array_merge($defaultAttributes, $attributes));
    }

    /**
     * Create test documents with various configurations
     */
    protected function createTestDocuments(int $count = 5): array
    {
        $documents = [];
        
        for ($i = 1; $i <= $count; $i++) {
            $documents[] = Document::factory()->create([
                'title' => "Test Document {$i}",
                'description' => "Description for test document {$i}",
                'category' => $this->getRandomCategory(),
                'supplier' => "Test Supplier {$i}",
                'status' => $this->getRandomStatus(),
            ]);
        }

        return $documents;
    }

    /**
     * Get random document category
     */
    protected function getRandomCategory(): string
    {
        $categories = ['Contract', 'Invoice', 'Report', 'Certificate', 'Manual'];
        return $categories[array_rand($categories)];
    }

    /**
     * Get random document status
     */
    protected function getRandomStatus(): string
    {
        $statuses = ['draft', 'active', 'archived'];
        return $statuses[array_rand($statuses)];
    }

    /**
     * Login user via browser
     */
    protected function loginUser(Browser $browser, User $user): Browser
    {
        return $browser->visit('/login')
                      ->type('email', $user->email)
                      ->type('password', 'password123')
                      ->press('Login')
                      ->assertPathIs('/');
    }

    /**
     * Wait for element to be visible with custom timeout
     */
    protected function waitForElement(Browser $browser, string $selector, int $timeout = 10): Browser
    {
        return $browser->waitFor($selector, $timeout);
    }

    /**
     * Assert element has specific CSS property
     */
    protected function assertElementStyle(Browser $browser, string $selector, string $property, string $value): void
    {
        $actualValue = $browser->script("
            return window.getComputedStyle(document.querySelector('{$selector}')).{$property};
        ")[0];
        
        $this->assertEquals($value, $actualValue, "Element {$selector} does not have {$property}: {$value}");
    }

    /**
     * Take screenshot with timestamp
     */
    protected function takeTimestampedScreenshot(Browser $browser, string $name): void
    {
        $timestamp = date('Y-m-d_H-i-s');
        $browser->screenshot("{$name}_{$timestamp}");
    }

    /**
     * Fill document form with test data
     */
    protected function fillDocumentForm(Browser $browser, array $data = []): Browser
    {
        $defaultData = [
            'title' => 'Test Document',
            'description' => 'Test document description',
            'category' => 'Contract',
            'supplier' => 'Test Supplier',
        ];

        $formData = array_merge($defaultData, $data);

        $browser->type('title', $formData['title'])
                ->type('description', $formData['description'])
                ->select('category', $formData['category'])
                ->type('supplier', $formData['supplier']);

        return $browser;
    }

    /**
     * Assert document appears in list
     */
    protected function assertDocumentInList(Browser $browser, string $title): void
    {
        $browser->visit('/documents')
                ->assertSee($title);
    }

    /**
     * Assert document details are correct
     */
    protected function assertDocumentDetails(Browser $browser, Document $document): void
    {
        $browser->visit('/documents/' . $document->id)
                ->assertSee($document->title)
                ->assertSee($document->description)
                ->assertSee($document->category)
                ->assertSee($document->supplier)
                ->assertPresent('@barcode-display');
    }

    /**
     * Test responsive design at different breakpoints
     */
    protected function testResponsiveDesign(Browser $browser, callable $testCallback): void
    {
        $breakpoints = [
            'mobile' => [375, 667],
            'tablet' => [768, 1024],
            'desktop' => [1920, 1080],
        ];

        foreach ($breakpoints as $name => $size) {
            $browser->resize($size[0], $size[1]);
            $testCallback($browser, $name, $size);
        }
    }

    /**
     * Test keyboard navigation
     */
    protected function testKeyboardNavigation(Browser $browser, array $elements): void
    {
        foreach ($elements as $element) {
            $browser->keys('body', ['{tab}'])
                    ->assertFocused($element);
        }
    }

    /**
     * Simulate network delay
     */
    protected function simulateNetworkDelay(Browser $browser, int $milliseconds = 1000): void
    {
        $browser->script("
            const originalFetch = window.fetch;
            window.fetch = function(...args) {
                return new Promise(resolve => {
                    setTimeout(() => {
                        resolve(originalFetch.apply(this, args));
                    }, {$milliseconds});
                });
            };
        ");
    }

    /**
     * Reset network simulation
     */
    protected function resetNetworkSimulation(Browser $browser): void
    {
        $browser->script("
            if (window.originalFetch) {
                window.fetch = window.originalFetch;
            }
        ");
    }

    /**
     * Assert page performance metrics
     */
    protected function assertPagePerformance(Browser $browser, array $thresholds = []): void
    {
        $defaultThresholds = [
            'loadTime' => 5000, // 5 seconds
            'domContentLoaded' => 3000, // 3 seconds
            'firstContentfulPaint' => 2000, // 2 seconds
        ];

        $thresholds = array_merge($defaultThresholds, $thresholds);

        $metrics = $browser->script("
            return {
                loadTime: performance.timing.loadEventEnd - performance.timing.navigationStart,
                domContentLoaded: performance.timing.domContentLoadedEventEnd - performance.timing.navigationStart,
                firstContentfulPaint: performance.getEntriesByType('paint').find(entry => entry.name === 'first-contentful-paint')?.startTime || 0
            };
        ")[0];

        foreach ($thresholds as $metric => $threshold) {
            if (isset($metrics[$metric])) {
                $this->assertLessThan(
                    $threshold,
                    $metrics[$metric],
                    "Performance metric {$metric} exceeded threshold: {$metrics[$metric]}ms > {$threshold}ms"
                );
            }
        }
    }

    /**
     * Assert accessibility attributes
     */
    protected function assertAccessibilityAttributes(Browser $browser, string $selector, array $attributes): void
    {
        foreach ($attributes as $attribute => $expectedValue) {
            $browser->assertAttribute($selector, $attribute, $expectedValue);
        }
    }

    /**
     * Test form validation
     */
    protected function testFormValidation(Browser $browser, array $validationTests): void
    {
        foreach ($validationTests as $test) {
            // Clear form
            if (isset($test['clear'])) {
                foreach ($test['clear'] as $field) {
                    $browser->clear($field);
                }
            }

            // Fill form with test data
            if (isset($test['fill'])) {
                foreach ($test['fill'] as $field => $value) {
                    $browser->type($field, $value);
                }
            }

            // Submit form
            $browser->press($test['submit'] ?? 'Save');

            // Assert validation results
            if (isset($test['expectErrors'])) {
                foreach ($test['expectErrors'] as $error) {
                    $browser->assertSee($error);
                }
            }

            if (isset($test['expectSuccess'])) {
                $browser->assertPathIs($test['expectSuccess']);
            }
        }
    }

    /**
     * Generate test file for upload
     */
    protected function generateTestFile(string $type = 'pdf', int $sizeKB = 100): string
    {
        $content = str_repeat('A', $sizeKB * 1024);
        $filename = 'test_file_' . time() . '.' . $type;
        $path = storage_path('app/testing/' . $filename);
        
        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }
        
        file_put_contents($path, $content);
        
        return $path;
    }

    /**
     * Clean up test files
     */
    protected function cleanupTestFiles(): void
    {
        $testDir = storage_path('app/testing');
        if (is_dir($testDir)) {
            $files = glob($testDir . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }
    }

    /**
     * Assert console has no errors
     */
    protected function assertNoConsoleErrors(Browser $browser): void
    {
        $logs = $browser->driver->manage()->getLog('browser');
        $errors = array_filter($logs, function ($log) {
            return $log['level'] === 'SEVERE';
        });

        $this->assertEmpty($errors, 'Console errors found: ' . json_encode($errors));
    }

    /**
     * Wait for AJAX requests to complete
     */
    protected function waitForAjax(Browser $browser, int $timeout = 10): Browser
    {
        return $browser->waitUntil('typeof jQuery !== "undefined" && jQuery.active === 0', $timeout);
    }

    /**
     * Scroll element into view
     */
    protected function scrollIntoView(Browser $browser, string $selector): Browser
    {
        $browser->script("document.querySelector('{$selector}').scrollIntoView();");
        return $browser;
    }
}
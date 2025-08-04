<?php

namespace Tests;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Illuminate\Support\Collection;
use Laravel\Dusk\TestCase as BaseTestCase;
use PHPUnit\Framework\Attributes\BeforeClass;
use Tests\Browser\Traits\E2ETestHelpers;

abstract class DuskTestCase extends BaseTestCase
{
    use E2ETestHelpers;

    /**
     * Prepare for Dusk test execution.
     */
    #[BeforeClass]
    public static function prepare(): void
    {
        if (! static::runningInSail()) {
            static::startChromeDriver(['--port=9515']);
        }
    }

    /**
     * Create the RemoteWebDriver instance.
     */
    protected function driver(): RemoteWebDriver
    {
        $config = config('dusk');
        $browserOptions = $config['browser_options'] ?? [];
        
        $arguments = collect([
            $this->shouldStartMaximized() ? '--start-maximized' : '--window-size=' . $config['browser_size']['width'] . ',' . $config['browser_size']['height'],
            '--disable-search-engine-choice-screen',
            '--disable-smooth-scrolling',
        ]);

        // Add additional browser options from config
        if ($browserOptions['disable_extensions'] ?? true) {
            $arguments->push('--disable-extensions');
        }
        
        if ($browserOptions['disable_dev_shm_usage'] ?? true) {
            $arguments->push('--disable-dev-shm-usage');
        }
        
        if ($browserOptions['no_sandbox'] ?? false) {
            $arguments->push('--no-sandbox');
        }
        
        if ($browserOptions['disable_web_security'] ?? false) {
            $arguments->push('--disable-web-security');
        }

        $arguments = $arguments->unless($this->hasHeadlessDisabled(), function (Collection $items) use ($browserOptions) {
            $headlessArgs = [];
            
            if ($browserOptions['disable_gpu'] ?? true) {
                $headlessArgs[] = '--disable-gpu';
            }
            
            if ($browserOptions['headless'] ?? true) {
                $headlessArgs[] = '--headless=new';
            }
            
            return $items->merge($headlessArgs);
        });

        $options = (new ChromeOptions)->addArguments($arguments->all());

        $driver = RemoteWebDriver::create(
            $_ENV['DUSK_DRIVER_URL'] ?? env('DUSK_DRIVER_URL') ?? 'http://localhost:9515',
            DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY, $options
            )
        );

        // Set timeouts from config
        $driver->manage()->timeouts()->implicitlyWait($config['implicit_wait'] ?? 10);
        $driver->manage()->timeouts()->pageLoadTimeout($config['page_load_timeout'] ?? 30);
        $driver->manage()->timeouts()->setScriptTimeout($config['script_timeout'] ?? 30);

        return $driver;
    }

    /**
     * Get selector from config
     */
    protected function selector(string $key): string
    {
        $selectors = config('dusk.selectors', []);
        return $selectors[$key] ?? "[data-testid=\"{$key}\"]";
    }

    /**
     * Setup method to run before each test
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Run migrations for in-memory database
        $this->artisan('migrate:fresh');
        
        // Create testing directory if it doesn't exist
        $testingDir = storage_path('app/testing');
        if (!file_exists($testingDir)) {
            mkdir($testingDir, 0755, true);
        }
    }

    /**
     * Cleanup method to run after each test
     */
    protected function tearDown(): void
    {
        $this->cleanupTestFiles();
        parent::tearDown();
    }
}

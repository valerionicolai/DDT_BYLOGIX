<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class SimpleLoginDebugTest extends DuskTestCase
{
    public function testSimpleLoginDebug()
    {
        $this->browse(function (Browser $browser) {
            echo "\n=== Simple Login Debug Test ===\n";
            
            echo "1. Getting CSRF cookie first...\n";
            $browser->visit('/sanctum/csrf-cookie')
                    ->pause(2000);
            
            echo "2. Visiting login page and waiting for Vue...\n";
            $browser->visit('/login')
                    ->pause(5000);
            
            echo "Current URL: " . $browser->driver->getCurrentURL() . "\n";
            echo "Page title: " . $browser->driver->getTitle() . "\n";
            
            echo "3. Checking if Vue app is loaded...\n";
            $vueLoaded = $browser->script('return !!document.querySelector("#app").__vue__');
            echo "Vue app detected: " . ($vueLoaded ? 'Yes' : 'No') . "\n";
            
            echo "4. Making direct API call to /api/auth/login...\n";
            $browser->script('
                window.loginResult = null;
                window.loginError = null;
                
                fetch("/api/auth/login", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "X-Requested-With": "XMLHttpRequest"
                    },
                    credentials: "same-origin",
                    body: JSON.stringify({
                        email: "admin@dttbylogix.com",
                        password: "password123"
                    })
                })
                .then(response => {
                    console.log("Response status:", response.status);
                    return response.json();
                })
                .then(data => {
                    console.log("Login response:", data);
                    window.loginResult = data;
                })
                .catch(error => {
                    console.error("Login error:", error);
                    window.loginError = "API Error";
                });
            ');
            
            $browser->pause(5000);
            
            echo "5. Getting API response result...\n";
            $loginResult = $browser->script('return window.loginResult');
            $loginError = $browser->script('return window.loginError');
            
            echo "Login API result: " . json_encode($loginResult, JSON_PRETTY_PRINT) . "\n";
            if ($loginError) {
                echo "Login error: " . (is_string($loginError) ? $loginError : json_encode($loginError)) . "\n";
            }
            
            // If login was successful, try to access dashboard directly
            if ($loginResult && isset($loginResult['success']) && $loginResult['success']) {
                echo "6. Login was successful, trying to access dashboard...\n";
                $browser->visit('/');
                $browser->pause(3000);
                
                $dashboardUrl = $browser->driver->getCurrentURL();
                echo "7. Dashboard URL: " . $dashboardUrl . "\n";
                
                if (strpos($dashboardUrl, '/login') !== false) {
                    echo "   Still redirected to login - session not maintained\n";
                } else {
                    echo "   Successfully accessed dashboard - session working\n";
                }
            }
            
            echo "8. Checking if user is now authenticated...\n";
            $browser->visit('/api/auth/user')
                    ->pause(2000);
            
            $pageSource = $browser->driver->getPageSource();
            echo "Auth user endpoint response: " . substr($pageSource, 0, 500) . "...\n";
            
            if (strpos($pageSource, '"id":') !== false) {
                echo "Protected endpoint returned user data (authenticated)\n";
            } else {
                echo "Protected endpoint did not return user data\n";
            }
            
            echo "9. Trying to access dashboard again...\n";
            $browser->visit('/')
                    ->pause(3000);
            
            $currentUrl = $browser->driver->getCurrentURL();
            $pageTitle = $browser->driver->getTitle();
            
            echo "Final URL: " . $currentUrl . "\n";
            echo "Final page title: " . $pageTitle . "\n";
            
            if (strpos($currentUrl, '/login') !== false) {
                echo "Still redirected to login - authentication failed\n";
            } else {
                echo "Successfully accessed dashboard - authentication worked\n";
            }
            
            echo "=== End Debug Test ===\n";
        });
    }
}
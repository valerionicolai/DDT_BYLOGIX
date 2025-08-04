<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class WebSessionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->app['config']->set('sanctum.stateful', ['localhost']);
        $this->app['config']->set('session.domain', 'localhost');
        $this->app['config']->set('session.secure', false);
    }

    public function test_web_session_persistence()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password')
        ]);

        // Use standard login endpoint with correct path
        $loginResponse = $this->withHeaders([
            'Origin' => 'http://localhost',
            'Accept' => 'application/json',
            'Referer' => 'http://localhost'
        ])->post('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password'
        ]);

        $loginResponse->dump(); // Debug output
        $loginResponse->assertStatus(200);
        $this->assertAuthenticated('web');

        // Now test session persistence
        $sessionResponse = $this->withHeaders([
            'Origin' => 'http://localhost',
            'Accept' => 'application/json',
            'Referer' => 'http://localhost'
        ])->get('/test-protected-web');

        $sessionResponse->assertStatus(200);
        
        // Verify session cookie is set
        $cookie = $loginResponse->getCookie(config('session.cookie'));
        $this->assertNotNull($cookie);
        $this->assertEquals('localhost', $cookie->getDomain());
        
        // Make authenticated request
        $response = $this->withCookie($cookie->getName(), $cookie->getValue())
            ->get('/test-protected-web');
            
        $response->assertStatus(200);
        $this->assertEquals($user->email, $response->json()['user']['email']);
    }
}
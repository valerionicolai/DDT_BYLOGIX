<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class SessionPersistenceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->app['config']->set('sanctum.stateful', ['localhost']);
        $this->app['config']->set('session.domain', 'localhost');
        $this->app['config']->set('session.secure', false);
    }

    public function test_session_persistence_after_login()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password')
        ]);

        // Use debug login endpoint
        $response = $this->withHeaders([
            'Origin' => 'http://localhost',
            'Accept' => 'application/json',
            'Referer' => 'http://localhost'
        ])->post('/debug-login', [
            'email' => 'test@example.com',
            'password' => 'password'
        ]);

        $response->assertStatus(200);
        $this->assertAuthenticated();
        
        // Verify session cookie is set
        $cookie = $response->getCookie(config('session.cookie'));
        $this->assertNotNull($cookie);
        $this->assertEquals('localhost', $cookie->getDomain());
        $this->assertTrue($cookie->isSecure());
        $this->assertTrue($cookie->isHttpOnly());
        
        // Make authenticated request
        $response = $this->withCookie($cookie->getName(), $cookie->getValue())
            ->get('/api/user');
            
        $response->assertStatus(200);
        $this->assertEquals($user->email, $response->json()['email']);
    }
}
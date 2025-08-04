<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DebugLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_debug_login_works()
    {
        // Create test user
        $user = \App\Models\User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password')
        ]);

        // Call debug-login endpoint
        $response = $this->withHeaders([
            'Origin' => 'http://localhost',
            'Accept' => 'application/json',
            'Referer' => 'http://localhost'
        ])->post('/debug-login', [
            'email' => 'test@example.com',
            'password' => 'password'
        ]);

        // Verify response
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }
}
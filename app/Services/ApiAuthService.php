<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class ApiAuthService
{
    protected string $baseUrl;
    protected array $defaultHeaders;

    public function __construct()
    {
        $this->baseUrl = config('app.url') . '/api';
        $this->defaultHeaders = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * Authenticate user with email and password
     */
    public function login(string $email, string $password, bool $remember = false): array
    {
        try {
            $response = Http::withHeaders($this->defaultHeaders)
                ->post($this->baseUrl . '/auth/login', [
                    'email' => $email,
                    'password' => $password,
                    'remember' => $remember,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Store token if provided (for API authentication)
                if (isset($data['data']['token'])) {
                    Session::put('api_token', $data['data']['token']);
                }
                
                // Store user data
                Session::put('api_user', $data['data']['user']);
                
                // Also authenticate the user in Laravel's auth system for Filament compatibility
                if (isset($data['data']['user']['id'])) {
                    $user = \App\Models\User::find($data['data']['user']['id']);
                    if ($user) {
                        \Illuminate\Support\Facades\Auth::login($user, $remember);
                    }
                }
                
                return [
                    'success' => true,
                    'data' => $data['data'],
                    'message' => $data['message'] ?? 'Login successful'
                ];
            }

            return [
                'success' => false,
                'message' => $response->json('message') ?? 'Login failed',
                'errors' => $response->json('errors') ?? []
            ];

        } catch (\Exception $e) {
            Log::error('API Login Error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Connection error. Please try again.',
                'errors' => []
            ];
        }
    }

    /**
     * Register a new user
     */
    public function register(array $userData): array
    {
        try {
            $response = Http::withHeaders($this->defaultHeaders)
                ->post($this->baseUrl . '/auth/register', $userData);

            if ($response->successful()) {
                $data = $response->json();
                
                // Store token and user data
                if (isset($data['data']['token'])) {
                    Session::put('api_token', $data['data']['token']);
                }
                Session::put('api_user', $data['data']['user']);
                
                // Also authenticate the user in Laravel's auth system for Filament compatibility
                if (isset($data['data']['user']['id'])) {
                    $user = \App\Models\User::find($data['data']['user']['id']);
                    if ($user) {
                        \Illuminate\Support\Facades\Auth::login($user);
                    }
                }
                
                return [
                    'success' => true,
                    'data' => $data['data'],
                    'message' => $data['message'] ?? 'Registration successful'
                ];
            }

            return [
                'success' => false,
                'message' => $response->json('message') ?? 'Registration failed',
                'errors' => $response->json('errors') ?? []
            ];

        } catch (\Exception $e) {
            Log::error('API Registration Error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Connection error. Please try again.',
                'errors' => []
            ];
        }
    }

    /**
     * Logout user
     */
    public function logout(): array
    {
        try {
            $token = Session::get('api_token');
            $headers = $this->defaultHeaders;
            
            if ($token) {
                $headers['Authorization'] = 'Bearer ' . $token;
            }

            $response = Http::withHeaders($headers)
                ->post($this->baseUrl . '/auth/logout');

            // Clear session data regardless of response
            Session::forget(['api_token', 'api_user']);
            
            // Also logout from Laravel's auth system
            \Illuminate\Support\Facades\Auth::logout();

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => $response->json('message') ?? 'Logout successful'
                ];
            }

            return [
                'success' => true, // Still consider it successful since we cleared local data
                'message' => 'Logged out locally'
            ];

        } catch (\Exception $e) {
            Log::error('API Logout Error: ' . $e->getMessage());
            
            // Clear session data even on error
            Session::forget(['api_token', 'api_user']);
            
            // Also logout from Laravel's auth system
            \Illuminate\Support\Facades\Auth::logout();
            
            return [
                'success' => true,
                'message' => 'Logged out locally'
            ];
        }
    }

    /**
     * Get current authenticated user
     */
    public function getCurrentUser(): array
    {
        try {
            $token = Session::get('api_token');
            
            if (!$token) {
                return [
                    'success' => false,
                    'message' => 'No authentication token found'
                ];
            }

            $response = Http::withHeaders(array_merge($this->defaultHeaders, [
                'Authorization' => 'Bearer ' . $token
            ]))->get($this->baseUrl . '/auth/user');

            if ($response->successful()) {
                $data = $response->json();
                
                // Update stored user data
                Session::put('api_user', $data['data']['user']);
                
                return [
                    'success' => true,
                    'data' => $data['data']
                ];
            }

            // If unauthorized, clear session
            if ($response->status() === 401) {
                Session::forget(['api_token', 'api_user']);
            }

            return [
                'success' => false,
                'message' => $response->json('message') ?? 'Failed to get user data'
            ];

        } catch (\Exception $e) {
            Log::error('API Get User Error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Connection error. Please try again.'
            ];
        }
    }

    /**
     * Refresh authentication token
     */
    public function refreshToken(): array
    {
        try {
            $token = Session::get('api_token');
            
            if (!$token) {
                return [
                    'success' => false,
                    'message' => 'No authentication token found'
                ];
            }

            $response = Http::withHeaders(array_merge($this->defaultHeaders, [
                'Authorization' => 'Bearer ' . $token
            ]))->post($this->baseUrl . '/auth/refresh');

            if ($response->successful()) {
                $data = $response->json();
                
                // Update token and user data
                if (isset($data['data']['token'])) {
                    Session::put('api_token', $data['data']['token']);
                }
                Session::put('api_user', $data['data']['user']);
                
                return [
                    'success' => true,
                    'data' => $data['data'],
                    'message' => $data['message'] ?? 'Token refreshed successfully'
                ];
            }

            // If unauthorized, clear session
            if ($response->status() === 401) {
                Session::forget(['api_token', 'api_user']);
            }

            return [
                'success' => false,
                'message' => $response->json('message') ?? 'Failed to refresh token'
            ];

        } catch (\Exception $e) {
            Log::error('API Refresh Token Error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Connection error. Please try again.'
            ];
        }
    }

    /**
     * Check if user is authenticated
     */
    public function isAuthenticated(): bool
    {
        // Check both API authentication and Laravel's auth system
        return (Session::has('api_token') && Session::has('api_user')) || \Illuminate\Support\Facades\Auth::check();
    }

    /**
     * Get stored user data
     */
    public function getStoredUser(): ?array
    {
        return Session::get('api_user');
    }

    /**
     * Get stored token
     */
    public function getStoredToken(): ?string
    {
        return Session::get('api_token');
    }

    /**
     * Make authenticated API request
     */
    public function makeAuthenticatedRequest(string $method, string $endpoint, array $data = []): array
    {
        try {
            $token = Session::get('api_token');
            
            if (!$token) {
                return [
                    'success' => false,
                    'message' => 'No authentication token found'
                ];
            }

            $headers = array_merge($this->defaultHeaders, [
                'Authorization' => 'Bearer ' . $token
            ]);

            $response = Http::withHeaders($headers)->{strtolower($method)}($this->baseUrl . $endpoint, $data);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            // If unauthorized, clear session
            if ($response->status() === 401) {
                Session::forget(['api_token', 'api_user']);
            }

            return [
                'success' => false,
                'message' => $response->json('message') ?? 'Request failed',
                'status' => $response->status()
            ];

        } catch (\Exception $e) {
            Log::error('API Request Error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Connection error. Please try again.'
            ];
        }
    }
}
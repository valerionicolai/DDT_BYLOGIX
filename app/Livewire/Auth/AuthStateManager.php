<?php

namespace App\Livewire\Auth;

use App\Services\ApiAuthService;
use Livewire\Attributes\On;
use Livewire\Component;

class AuthStateManager extends Component
{
    public ?array $user = null;
    public bool $isAuthenticated = false;
    public bool $isLoading = false;
    public string $lastActivity = '';
    public int $sessionTimeout = 3600; // 1 hour in seconds

    protected ApiAuthService $apiAuthService;

    public function boot(ApiAuthService $apiAuthService)
    {
        $this->apiAuthService = $apiAuthService;
    }

    public function mount()
    {
        $this->initializeAuth();
    }

    public function initializeAuth()
    {
        $this->isLoading = true;

        try {
            if ($this->apiAuthService->isAuthenticated()) {
                $this->user = $this->apiAuthService->getStoredUser();
                $this->isAuthenticated = true;
                $this->updateLastActivity();
                
                // Verify token is still valid
                $this->verifyToken();
            } else {
                $this->clearAuthState();
            }
        } catch (\Exception $e) {
            $this->clearAuthState();
        } finally {
            $this->isLoading = false;
        }
    }

    public function verifyToken()
    {
        try {
            $result = $this->apiAuthService->getCurrentUser();
            
            if ($result['success']) {
                $this->user = $result['data']['user'];
                $this->isAuthenticated = true;
                $this->updateLastActivity();
                
                // Dispatch auth verified event
                $this->dispatch('auth-verified', ['user' => $this->user]);
            } else {
                $this->clearAuthState();
                $this->dispatch('auth-expired');
            }
        } catch (\Exception $e) {
            $this->clearAuthState();
            $this->dispatch('auth-error', ['message' => 'Session verification failed']);
        }
    }

    public function refreshToken()
    {
        $this->isLoading = true;

        try {
            $result = $this->apiAuthService->refreshToken();
            
            if ($result['success']) {
                $this->user = $result['data']['user'];
                $this->isAuthenticated = true;
                $this->updateLastActivity();
                
                // Dispatch token refreshed event
                $this->dispatch('token-refreshed', ['user' => $this->user]);
            } else {
                $this->clearAuthState();
                $this->dispatch('auth-expired');
            }
        } catch (\Exception $e) {
            $this->clearAuthState();
            $this->dispatch('auth-error', ['message' => 'Token refresh failed']);
        } finally {
            $this->isLoading = false;
        }
    }

    public function logout()
    {
        $this->isLoading = true;

        try {
            $result = $this->apiAuthService->logout();
            
            $this->clearAuthState();
            
            // Dispatch logout event
            $this->dispatch('auth-logout', ['message' => $result['message'] ?? 'Logged out successfully']);
            
            // Redirect to login
            return $this->redirect('/login');
        } catch (\Exception $e) {
            $this->clearAuthState();
            $this->dispatch('auth-error', ['message' => 'Logout failed']);
        } finally {
            $this->isLoading = false;
        }
    }

    #[On('auth-success')]
    public function handleAuthSuccess($data)
    {
        $this->user = $data['user'];
        $this->isAuthenticated = true;
        $this->updateLastActivity();
        
        // Start session monitoring
        $this->startSessionMonitoring();
    }

    #[On('auth-error')]
    public function handleAuthError($data)
    {
        $this->clearAuthState();
    }

    #[On('user-activity')]
    public function handleUserActivity()
    {
        $this->updateLastActivity();
    }

    public function updateLastActivity()
    {
        $this->lastActivity = now()->toISOString();
    }

    public function clearAuthState()
    {
        $this->user = null;
        $this->isAuthenticated = false;
        $this->lastActivity = '';
    }

    public function startSessionMonitoring()
    {
        // This will be handled by Alpine.js on the frontend
        $this->dispatch('start-session-monitoring', [
            'timeout' => $this->sessionTimeout,
            'lastActivity' => $this->lastActivity
        ]);
    }

    public function checkSessionTimeout()
    {
        if (!$this->isAuthenticated || !$this->lastActivity) {
            return;
        }

        $lastActivityTime = \Carbon\Carbon::parse($this->lastActivity);
        $timeSinceLastActivity = now()->diffInSeconds($lastActivityTime);

        if ($timeSinceLastActivity > $this->sessionTimeout) {
            $this->logout();
            $this->dispatch('session-timeout');
        }
    }

    public function getUserRole(): ?string
    {
        return $this->user['role'] ?? null;
    }

    public function isAdmin(): bool
    {
        return $this->getUserRole() === 'admin';
    }

    public function isManager(): bool
    {
        return in_array($this->getUserRole(), ['admin', 'manager']);
    }

    public function hasPermission(string $permission): bool
    {
        // Basic role-based permission check
        $role = $this->getUserRole();
        
        switch ($permission) {
            case 'admin':
                return $role === 'admin';
            case 'manager':
                return in_array($role, ['admin', 'manager']);
            case 'user':
                return in_array($role, ['admin', 'manager', 'user']);
            default:
                return false;
        }
    }

    public function render()
    {
        return view('livewire.auth.auth-state-manager');
    }
}
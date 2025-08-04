<?php

namespace App\Livewire\Auth;

use App\Services\ApiAuthService;
use Livewire\Attributes\Rule;
use Livewire\Component;

class UserProfile extends Component
{
    protected string $layout = 'layouts.auth';
    
    public ?array $user = null;
    public bool $isLoading = false;
    public bool $isEditing = false;
    public string $successMessage = '';
    public string $errorMessage = '';

    // Profile fields
    #[Rule('required|string|max:255')]
    public string $name = '';

    #[Rule('required|email|max:255')]
    public string $email = '';

    // Password change fields
    #[Rule('nullable|min:8')]
    public string $current_password = '';

    #[Rule('nullable|min:8')]
    public string $new_password = '';

    #[Rule('nullable|same:new_password')]
    public string $new_password_confirmation = '';

    protected ApiAuthService $apiAuthService;

    public function boot(ApiAuthService $apiAuthService)
    {
        $this->apiAuthService = $apiAuthService;
    }

    public function mount()
    {
        $this->loadUserProfile();
    }

    public function loadUserProfile()
    {
        $this->isLoading = true;
        $this->clearMessages();

        try {
            if (!$this->apiAuthService->isAuthenticated()) {
                return $this->redirect('/login');
            }

            // Get current user data
            $result = $this->apiAuthService->getCurrentUser();
            
            if ($result['success']) {
                $this->user = $result['data']['user'];
                $this->name = $this->user['name'];
                $this->email = $this->user['email'];
            } else {
                $this->errorMessage = 'Failed to load user profile';
                return $this->redirect('/login');
            }
        } catch (\Exception $e) {
            $this->errorMessage = 'An error occurred while loading your profile';
        } finally {
            $this->isLoading = false;
        }
    }

    public function startEditing()
    {
        $this->isEditing = true;
        $this->clearMessages();
    }

    public function cancelEditing()
    {
        $this->isEditing = false;
        $this->clearMessages();
        
        // Reset fields to original values
        if ($this->user) {
            $this->name = $this->user['name'];
            $this->email = $this->user['email'];
        }
        
        // Clear password fields
        $this->current_password = '';
        $this->new_password = '';
        $this->new_password_confirmation = '';
    }

    public function updateProfile()
    {
        $this->isLoading = true;
        $this->clearMessages();

        // Validate basic profile fields
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        // Validate password fields if provided
        if ($this->new_password) {
            $this->validate([
                'current_password' => 'required|min:8',
                'new_password' => 'required|min:8',
                'new_password_confirmation' => 'required|same:new_password',
            ]);
        }

        try {
            $updateData = [
                'name' => $this->name,
                'email' => $this->email,
            ];

            // Add password fields if changing password
            if ($this->new_password) {
                $updateData['current_password'] = $this->current_password;
                $updateData['password'] = $this->new_password;
                $updateData['password_confirmation'] = $this->new_password_confirmation;
            }

            // Make API request to update profile
            $result = $this->apiAuthService->makeAuthenticatedRequest('PUT', '/users/profile', $updateData);

            if ($result['success']) {
                $this->user = $result['data']['user'] ?? $this->user;
                $this->successMessage = 'Profile updated successfully';
                $this->isEditing = false;
                
                // Clear password fields
                $this->current_password = '';
                $this->new_password = '';
                $this->new_password_confirmation = '';
                
                // Dispatch profile updated event
                $this->dispatch('profile-updated', ['user' => $this->user]);
            } else {
                $this->errorMessage = $result['message'] ?? 'Failed to update profile';
            }
        } catch (\Exception $e) {
            $this->errorMessage = 'An error occurred while updating your profile';
        } finally {
            $this->isLoading = false;
        }
    }

    public function refreshProfile()
    {
        $this->loadUserProfile();
    }

    public function clearMessages()
    {
        $this->successMessage = '';
        $this->errorMessage = '';
        $this->resetErrorBag();
    }

    public function updatedName()
    {
        $this->clearMessages();
    }

    public function updatedEmail()
    {
        $this->clearMessages();
    }

    public function updatedCurrentPassword()
    {
        $this->clearMessages();
    }

    public function updatedNewPassword()
    {
        $this->clearMessages();
    }

    public function updatedNewPasswordConfirmation()
    {
        $this->clearMessages();
    }

    public function getUserRole(): string
    {
        return $this->user['role'] ?? 'user';
    }

    public function getUserInitials(): string
    {
        if (!$this->user || !$this->user['name']) {
            return 'U';
        }

        $nameParts = explode(' ', $this->user['name']);
        $initials = '';
        
        foreach ($nameParts as $part) {
            if (!empty($part)) {
                $initials .= strtoupper($part[0]);
            }
        }

        return $initials ?: 'U';
    }

    public function getFormattedJoinDate(): string
    {
        if (!$this->user || !$this->user['created_at']) {
            return 'Unknown';
        }

        return \Carbon\Carbon::parse($this->user['created_at'])->format('F j, Y');
    }

    public function render()
    {
        return view('livewire.auth.user-profile');
    }
}
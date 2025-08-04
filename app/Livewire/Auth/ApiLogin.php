<?php

namespace App\Livewire\Auth;

use App\Services\ApiAuthService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;

class ApiLogin extends Component
{
    protected string $layout = 'auth-wrapper';
    
    #[Rule('required|email')]
    public string $email = '';

    #[Rule('required|min:6')]
    public string $password = '';

    public bool $remember = false;
    public bool $loading = false;
    public string $errorMessage = '';
    public array $apiErrors = [];

    protected ApiAuthService $apiAuthService;

    public function boot(ApiAuthService $apiAuthService)
    {
        $this->apiAuthService = $apiAuthService;
    }

    public function mount()
    {
        // Redirect if already authenticated
        if ($this->apiAuthService->isAuthenticated()) {
            return $this->redirect('/admin');
        }
    }

    public function login()
    {
        $this->loading = true;
        $this->errorMessage = '';
        $this->apiErrors = [];

        // Validate input
        $this->validate();

        try {
            // Attempt login via API
            $result = $this->apiAuthService->login($this->email, $this->password, $this->remember);

            if ($result['success']) {
                // Dispatch success event
                $this->dispatch('auth-success', [
                    'user' => $result['data']['user'],
                    'message' => $result['message']
                ]);

                // Redirect to admin panel
                return $this->redirect('/admin');
            } else {
                $this->errorMessage = $result['message'];
                $this->apiErrors = $result['errors'] ?? [];
                
                // Dispatch error event
                $this->dispatch('auth-error', [
                    'message' => $result['message'],
                    'errors' => $result['errors'] ?? []
                ]);
            }
        } catch (\Exception $e) {
            $this->errorMessage = 'An unexpected error occurred. Please try again.';
            
            // Dispatch error event
            $this->dispatch('auth-error', [
                'message' => $this->errorMessage
            ]);
        } finally {
            $this->loading = false;
        }
    }

    public function clearErrors()
    {
        $this->errorMessage = '';
        $this->apiErrors = [];
        $this->resetErrorBag();
    }

    public function updatedEmail()
    {
        $this->clearErrors();
    }

    public function updatedPassword()
    {
        $this->clearErrors();
    }

    #[Layout('layouts.auth')]
    public function render()
    {
        return view('livewire.auth.api-login');
    }
}
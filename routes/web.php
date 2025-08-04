<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Livewire\TestComponent;
use App\Livewire\Auth\ApiLogin;
use App\Livewire\Auth\ApiRegister;
use App\Livewire\Auth\UserProfile;

// Test route for Livewire component
Route::get('/livewire-test', TestComponent::class)->name('livewire.test');

// API Authentication routes
Route::get('/login', ApiLogin::class)->name('login');
Route::get('/register', ApiRegister::class)->name('register');

// Protected routes (require authentication)
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/profile', UserProfile::class)->name('profile');
});

// Main route - redirect to admin panel if authenticated, otherwise to login
Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/admin');
    }
    return redirect('/admin/login');
});

// Fallback route - redirect to admin panel
Route::get('/{any}', function () {
    if (Auth::check()) {
        return redirect('/admin');
    }
    return redirect('/admin/login');
})->where('any', '.*');

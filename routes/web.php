<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Livewire\TestComponent;
use App\Livewire\Auth\ApiLogin;
use App\Livewire\Auth\ApiRegister;
use App\Livewire\Auth\UserProfile;
use App\Http\Controllers\PublicController;

// Test route for Livewire component
Route::get('/livewire-test', TestComponent::class)->name('livewire.test');

// API Authentication routes
Route::get('/login', ApiLogin::class)->name('login');
Route::get('/register', ApiRegister::class)->name('register');

// Protected routes (require authentication)
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/profile', UserProfile::class)->name('profile');
});

// Public QR Code routes (no authentication required)
Route::prefix('public')->name('public.')->group(function () {
    // QR Code scanner page
    Route::get('/scanner', [PublicController::class, 'scanner'])->name('scanner');
    
    // Public material and document display
    Route::get('/material/{id}', [PublicController::class, 'showMaterial'])->name('material');
    Route::get('/document/{id}', [PublicController::class, 'showDocument'])->name('document');
    
    // QR Code scanning endpoint
    Route::get('/qr/{qrCode}', [PublicController::class, 'scanQRCode'])->name('qr.scan');
});

// API routes for QR codes (no authentication required for public access)
Route::prefix('api/public')->name('api.public.')->group(function () {
    // Get QR code image
    Route::get('/qr/{type}/{id}', [PublicController::class, 'getQRCodeImage'])->name('qr.image');
    
    // Batch QR code generation (might need authentication in production)
    Route::post('/qr/batch', [PublicController::class, 'generateBatchQRCodes'])->name('qr.batch');
});

// Main route - redirect to admin panel if authenticated, otherwise to login
Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/admin');
    }
    return redirect('/admin/login');
});

// Fallback route - redirect to admin panel (exclude public routes)
Route::get('/{any}', function () {
    if (Auth::check()) {
        return redirect('/admin');
    }
    return redirect('/admin/login');
})->where('any', '^(?!public|api).*');

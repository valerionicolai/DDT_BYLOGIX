<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PublicController;

// Public material and document display (still accessible without auth)
Route::prefix('public')->name('public.')->group(function () {
    Route::get('/material/{id}', [PublicController::class, 'showMaterial'])->name('material');
    Route::get('/document/{id}', [PublicController::class, 'showDocument'])->name('document');
    Route::get('/qr/{qrCode}', [PublicController::class, 'scanQRCode'])->name('qr.scan');
});

// Login route - redirect to Filament admin login
Route::get('/login', function () {
    return redirect('/admin/login');
})->name('login');

// Authenticated scanner route
Route::middleware('auth')->group(function () {
    Route::get('/scanner', [PublicController::class, 'scanner'])->name('scanner');
});

// API routes for QR codes (no authentication required for public access)
Route::prefix('api/public')->name('api.public.')->group(function () {
    // Get QR code image
    Route::get('/qr/{type}/{id}', [PublicController::class, 'getQRCodeImage'])->name('qr.image');
    
    // Batch QR code generation (might need authentication in production)
    Route::post('/qr/batch', [PublicController::class, 'generateBatchQRCodes'])->name('qr.batch');
});

// Main route - redirect to admin panel
Route::get('/', function () {
    return redirect('/admin');
});

// Fallback route - redirect to admin panel (exclude public and api routes)
Route::get('/{any}', function () {
    return redirect('/admin');
})->where('any', '^(?!public|api|admin).*');

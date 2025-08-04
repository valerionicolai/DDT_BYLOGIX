<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\TestComponent;

// Test route for Livewire component
Route::get('/livewire-test', TestComponent::class)->name('livewire.test');

// Rotta principale per l'applicazione Vue.js
Route::get('/', function () {
    return view('app');
});

// Rotta di fallback per Vue Router (SPA)
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');

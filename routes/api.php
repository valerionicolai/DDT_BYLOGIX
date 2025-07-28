<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\ProjectController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route pubbliche per l'autenticazione
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
});

// Route protette che richiedono autenticazione
Route::middleware('auth:sanctum')->group(function () {
    // Route di autenticazione protette
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::get('/user', [AuthController::class, 'user']);
    });

    // Route per verificare l'autenticazione (endpoint di test)
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Route per la gestione dei clients
    Route::prefix('clients')->group(function () {
        Route::get('/stats', [ClientController::class, 'stats']);
        Route::get('/', [ClientController::class, 'index']);
        Route::post('/', [ClientController::class, 'store']);
        Route::get('/{id}', [ClientController::class, 'show']);
        Route::put('/{id}', [ClientController::class, 'update']);
        Route::patch('/{id}', [ClientController::class, 'update']);
        Route::delete('/{id}', [ClientController::class, 'destroy']);
    });

    // Project routes
    Route::get('/projects/stats', [ProjectController::class, 'stats']);
    Route::put('/projects/{id}/progress', [ProjectController::class, 'updateProgress']);
    Route::apiResource('projects', ProjectController::class);
});

// Route di fallback per API non trovate
Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'Endpoint API non trovato',
        'error' => 'Not Found'
    ], 404);
});
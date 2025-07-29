<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\MaterialTypeController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\UserController;
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
        Route::post('/refresh', [AuthController::class, 'refreshToken']);
        Route::get('/user', [AuthController::class, 'user']);
    });

    // Route per verificare l'autenticazione (endpoint di test)
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Route per la gestione dei clients (admin only for create/update/delete)
    Route::prefix('clients')->group(function () {
        Route::get('/stats', [ClientController::class, 'stats'])->middleware('admin');
        Route::get('/', [ClientController::class, 'index']);
        Route::get('/{id}', [ClientController::class, 'show']);
        
        // Admin-only operations
        Route::middleware('admin')->group(function () {
            Route::post('/', [ClientController::class, 'store']);
            Route::put('/{id}', [ClientController::class, 'update']);
            Route::patch('/{id}', [ClientController::class, 'update']);
            Route::delete('/{id}', [ClientController::class, 'destroy']);
        });
    });

    // Project routes (admin only for create/update/delete)
    Route::prefix('projects')->group(function () {
        Route::get('/stats', [ProjectController::class, 'stats'])->middleware('admin');
        Route::get('/', [ProjectController::class, 'index']);
        Route::get('/{id}', [ProjectController::class, 'show']);
        
        // Admin-only operations
        Route::middleware('admin')->group(function () {
            Route::post('/', [ProjectController::class, 'store']);
            Route::put('/{id}', [ProjectController::class, 'update']);
            Route::patch('/{id}', [ProjectController::class, 'update']);
            Route::delete('/{id}', [ProjectController::class, 'destroy']);
            Route::put('/{id}/progress', [ProjectController::class, 'updateProgress']);
        });
    });

    // Material Types routes (admin only for create/update/delete)
    Route::prefix('material-types')->group(function () {
        Route::get('/stats', [MaterialTypeController::class, 'stats'])->middleware('admin');
        Route::get('/categories', [MaterialTypeController::class, 'categories']);
        Route::get('/units-of-measure', [MaterialTypeController::class, 'unitsOfMeasure']);
        Route::get('/', [MaterialTypeController::class, 'index']);
        Route::get('/{id}', [MaterialTypeController::class, 'show']);
        
        // Admin-only operations
        Route::middleware('admin')->group(function () {
            Route::post('/', [MaterialTypeController::class, 'store']);
            Route::put('/{id}', [MaterialTypeController::class, 'update']);
            Route::patch('/{id}', [MaterialTypeController::class, 'update']);
            Route::delete('/{id}', [MaterialTypeController::class, 'destroy']);
        });
    });

    // Document routes
    Route::prefix('documents')->group(function () {
        // Public search routes (for barcode scanning)
        Route::get('/search', [DocumentController::class, 'searchByBarcode']);
        
        // Export route
        Route::get('/export/csv', [DocumentController::class, 'exportCsv']);
        
        // Standard CRUD routes
        Route::get('/', [DocumentController::class, 'index']);
        Route::get('/{document}', [DocumentController::class, 'show']);
        Route::post('/', [DocumentController::class, 'store']);
        Route::put('/{document}', [DocumentController::class, 'update']);
        Route::patch('/{document}', [DocumentController::class, 'update']);
        Route::delete('/{document}', [DocumentController::class, 'destroy']);
        
        // Special routes
        Route::post('/{document}/regenerate-barcode', [DocumentController::class, 'regenerateBarcode']);
        Route::get('/{document}/download', [DocumentController::class, 'download']);
    });

    // User management routes (admin only for most operations)
    Route::prefix('users')->group(function () {
        // Profile route accessible to all authenticated users
        Route::get('/profile', [UserController::class, 'profile']);
        
        // Admin-only routes
        Route::middleware('admin')->group(function () {
            Route::get('/stats', [UserController::class, 'stats']);
            Route::get('/', [UserController::class, 'index']);
            Route::post('/', [UserController::class, 'store']);
            Route::get('/{id}', [UserController::class, 'show']);
            Route::put('/{id}', [UserController::class, 'update']);
            Route::patch('/{id}', [UserController::class, 'update']);
            Route::delete('/{id}', [UserController::class, 'destroy']);
            Route::patch('/{id}/role', [UserController::class, 'updateRole']);
        });
    });
});

// Route di fallback per API non trovate
Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'Endpoint API non trovato',
        'error' => 'Not Found'
    ], 404);
});
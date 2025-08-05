<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\MaterialTypeController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\ProjectStateController;
use App\Http\Controllers\ProjectPriorityController;
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

    // Project State Management routes
    Route::prefix('project-states')->group(function () {
        // Public read routes (accessible to all authenticated users)
        Route::get('/', [ProjectStateController::class, 'index']);
        Route::get('/options', [ProjectStateController::class, 'options']);
        Route::get('/statistics', [ProjectStateController::class, 'statistics']);
        Route::get('/{state}/projects', [ProjectStateController::class, 'projectsByState']);
        Route::get('/{state}/eligible-projects', [ProjectStateController::class, 'eligibleProjects']);
        Route::get('/overdue-projects', [ProjectStateController::class, 'overdueProjects']);
        
        // Project-specific state routes
        Route::get('/projects/{project}/valid-transitions', [ProjectStateController::class, 'validTransitions']);
        
        // Admin-only state management operations
        Route::middleware('admin')->group(function () {
            Route::post('/projects', [ProjectStateController::class, 'store']);
            Route::put('/projects/{project}', [ProjectStateController::class, 'update']);
            Route::patch('/projects/{project}/transition', [ProjectStateController::class, 'transition']);
            Route::post('/bulk-transition', [ProjectStateController::class, 'bulkTransition']);
            Route::post('/archive-eligible', [ProjectStateController::class, 'archiveEligible']);
            Route::post('/review-overdue', [ProjectStateController::class, 'reviewOverdue']);
        });
    });

    // Project Priority Management routes
    Route::prefix('project-priorities')->group(function () {
        // Public read routes (accessible to all authenticated users)
        Route::get('/', [ProjectPriorityController::class, 'index']);
        Route::get('/options', [ProjectPriorityController::class, 'options']);
        Route::get('/statistics', [ProjectPriorityController::class, 'statistics']);
        Route::get('/{priority}/projects', [ProjectPriorityController::class, 'getProjectsByPriority']);
        Route::get('/urgent-projects', [ProjectPriorityController::class, 'getUrgentProjects']);
        Route::get('/high-priority-projects', [ProjectPriorityController::class, 'getHighPriorityProjects']);
        Route::get('/ordered-by-priority', [ProjectPriorityController::class, 'getProjectsOrderedByPriority']);
        Route::get('/needing-escalation', [ProjectPriorityController::class, 'getProjectsNeedingEscalation']);
        
        // Admin-only priority management operations
        Route::middleware('admin')->group(function () {
            Route::post('/projects', [ProjectPriorityController::class, 'store']);
            Route::put('/projects/{project}', [ProjectPriorityController::class, 'update']);
            Route::patch('/projects/{project}/change-priority', [ProjectPriorityController::class, 'changePriority']);
            Route::patch('/projects/{project}/escalate', [ProjectPriorityController::class, 'escalatePriority']);
            Route::patch('/projects/{project}/de-escalate', [ProjectPriorityController::class, 'deEscalatePriority']);
            Route::post('/bulk-change-priority', [ProjectPriorityController::class, 'bulkChangePriority']);
            Route::post('/auto-escalate', [ProjectPriorityController::class, 'autoEscalateProjects']);
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
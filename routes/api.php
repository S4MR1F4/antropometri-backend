<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ExportController;
use App\Http\Controllers\Api\MeasurementController;
use App\Http\Controllers\Api\SubjectController;
use App\Http\Controllers\Api\SyncController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded within a "api" middleware group. Enjoy building your API!
|
| Per 07_api_specification.md and 09_security_access.md
|
*/

// Public routes (no authentication required)
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Protected routes (require authentication)
Route::middleware(['auth:sanctum'])->group(function () {

    // Auth routes
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me'])->name('auth.me');
    });

    // Subjects CRUD
    Route::apiResource('subjects', SubjectController::class);

    // Measurements
    Route::get('/measurements', [MeasurementController::class, 'history']);
    Route::apiResource('subjects.measurements', MeasurementController::class)
        ->except(['update'])
        ->shallow();

    // Offline Sync (authenticated users)
    Route::post('/sync/measurements', [SyncController::class, 'syncMeasurements']);

    // Admin only routes
    Route::middleware(['admin'])->prefix('admin')->group(function () {
        // User management
        Route::get('/users', [AdminController::class, 'users']);
        Route::post('/users', [AdminController::class, 'storeUser']);
        Route::put('/users/{user}', [AdminController::class, 'updateUser']);
        Route::delete('/users/{user}', [AdminController::class, 'destroyUser']);

        // Statistics
        Route::get('/statistics', [AdminController::class, 'statistics']);

        // Exports
        Route::prefix('export')->group(function () {
            Route::get('/excel', [ExportController::class, 'exportExcel']);
            Route::get('/pdf', [ExportController::class, 'exportPdf']);
        });
    });
});

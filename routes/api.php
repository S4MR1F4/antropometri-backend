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
Route::get('/health', [\App\Http\Controllers\HealthController::class, 'check']);

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});

// Protected routes (require authentication)
Route::middleware(['auth:sanctum'])->group(function () {

    // Auth routes
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me'])->name('auth.me');
        Route::post('/update-profile', [AuthController::class, 'updateProfile']);
        Route::post('/change-password', [AuthController::class, 'changePassword']);
    });

    // Notifications
    Route::get('/notifications', [\App\Http\Controllers\Api\NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\Api\NotificationController::class, 'markAsRead']);

    // Subjects CRUD
    Route::apiResource('subjects', SubjectController::class);

    // Measurements
    Route::get('/measurements/grouped', [MeasurementController::class, 'groupedHistory']);
    Route::get('/measurements', [MeasurementController::class, 'history']);
    Route::apiResource('subjects.measurements', MeasurementController::class)
        ->except(['update'])
        ->shallow();

    // Offline Sync (authenticated users)
    Route::post('/sync/measurements', [SyncController::class, 'syncMeasurements']);

    // Exports (Authenticated)
    Route::prefix('export')->group(function () {
        Route::get('/excel', [ExportController::class, 'exportExcel']);
        Route::get('/pdf', [ExportController::class, 'exportPdf']);
    });

    // Admin only routes
    Route::middleware(['admin'])->prefix('admin')->group(function () {
        // User management
        Route::get('/users', [AdminController::class, 'users']);
        Route::post('/users', [AdminController::class, 'storeUser']);
        Route::put('/users/{user}', [AdminController::class, 'updateUser']);
        Route::delete('/users/{user}', [AdminController::class, 'destroyUser']);

        // Statistics
        Route::get('/statistics', [AdminController::class, 'statistics']);


    });
});

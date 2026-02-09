<?php

use Illuminate\Support\Facades\Route;

Route::any('{any}', function () {
    return response()->json([
        'success' => false,
        'message' => 'Web access is restricted. Please use the API endpoints at /api.',
    ], 403);
})->where('any', '.*');

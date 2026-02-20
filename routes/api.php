<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\DeviceAuthController;
use App\Http\Controllers\UsageAnalyticsController;
use App\Http\Controllers\UsageCommitController;

// Device auth endpoints (public, called by CLI)
Route::post('/auth/device/code', [DeviceAuthController::class, 'requestCode']);
Route::post('/auth/device/token', [DeviceAuthController::class, 'pollToken']);

// Usage endpoints for Go proxy (internal, should be protected by proxy secret in production)
Route::post('/usage/pre-check', [UsageCommitController::class, 'preCheck']);
Route::post('/usage/commit', [UsageCommitController::class, 'commit']);

Route::middleware(['auth:sanctum'])->group(function () {
    // User analytics endpoints
    Route::get('/usage/quota', [UsageAnalyticsController::class, 'getQuota']);
    Route::get('/usage/summary', [UsageAnalyticsController::class, 'getUserSummary']);
    Route::get('/usage/by-model', [UsageAnalyticsController::class, 'getUserUsageByModel']);
    Route::get('/usage/daily', [UsageAnalyticsController::class, 'getDailyAnalytics']);
    Route::get('/usage/hourly', [UsageAnalyticsController::class, 'getHourlyAnalytics']);
    Route::get('/usage/top-models', [UsageAnalyticsController::class, 'getTopModels']);

    // Admin analytics endpoint
    Route::middleware(['admin'])->group(function () {
        Route::get('/usage/all-users', [UsageAnalyticsController::class, 'getAllUsersSummary']);
    });
});

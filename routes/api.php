<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\DeviceAuthController;
use App\Http\Controllers\UsageAnalyticsController;
use App\Http\Controllers\InternalApiController;

// Device auth endpoints (public, called by CLI)
Route::post('/auth/device/code', [DeviceAuthController::class, 'requestCode']);
Route::post('/auth/device/token', [DeviceAuthController::class, 'pollToken']);

// Internal API for Go proxy (protected by shared secret)
Route::middleware('internal.secret')->prefix('internal')->group(function () {
    Route::get('/runtime-config', [InternalApiController::class, 'runtimeConfig']);
    Route::post('/commit-usage', [InternalApiController::class, 'commitUsage']);
});

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

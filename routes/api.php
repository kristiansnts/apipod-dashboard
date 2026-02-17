<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsageAnalyticsController;

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

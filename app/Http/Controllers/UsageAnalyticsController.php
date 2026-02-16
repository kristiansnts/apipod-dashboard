<?php

namespace App\Http\Controllers;

use App\Services\TokenUsageService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class UsageAnalyticsController extends Controller
{
    protected $tokenUsageService;

    public function __construct(TokenUsageService $tokenUsageService)
    {
        $this->tokenUsageService = $tokenUsageService;
    }

    /**
     * Get current user's usage summary
     */
    public function getUserSummary(Request $request)
    {
        $userId = Auth::id();
        $startDate = $request->has('start_date') ? Carbon::parse($request->start_date) : null;
        $endDate = $request->has('end_date') ? Carbon::parse($request->end_date) : null;

        $summary = $this->tokenUsageService->calculateUserCost($userId, $startDate, $endDate);

        return response()->json([
            'success' => true,
            'data' => $summary
        ]);
    }

    /**
     * Get current user's usage by model
     */
    public function getUserUsageByModel(Request $request)
    {
        $userId = Auth::id();
        $startDate = $request->has('start_date') ? Carbon::parse($request->start_date) : null;
        $endDate = $request->has('end_date') ? Carbon::parse($request->end_date) : null;

        $breakdown = $this->tokenUsageService->getUserUsageByModel($userId, $startDate, $endDate);

        return response()->json([
            'success' => true,
            'data' => $breakdown
        ]);
    }

    /**
     * Get daily usage analytics for current user
     */
    public function getDailyAnalytics(Request $request)
    {
        $userId = Auth::id();
        $startDate = $request->has('start_date') ? Carbon::parse($request->start_date) : Carbon::now()->subDays(30);
        $endDate = $request->has('end_date') ? Carbon::parse($request->end_date) : Carbon::now();

        $analytics = $this->tokenUsageService->getDailyUsageAnalytics($userId, $startDate, $endDate);

        return response()->json([
            'success' => true,
            'data' => $analytics
        ]);
    }

    /**
     * Get hourly usage for a specific date
     */
    public function getHourlyAnalytics(Request $request)
    {
        $userId = Auth::id();
        $date = $request->has('date') ? Carbon::parse($request->date) : Carbon::today();

        $analytics = $this->tokenUsageService->getHourlyUsage($userId, $date);

        return response()->json([
            'success' => true,
            'data' => $analytics
        ]);
    }

    /**
     * Get top models by cost
     */
    public function getTopModels(Request $request)
    {
        $userId = Auth::id();
        $limit = $request->get('limit', 5);
        $startDate = $request->has('start_date') ? Carbon::parse($request->start_date) : null;
        $endDate = $request->has('end_date') ? Carbon::parse($request->end_date) : null;

        $topModels = $this->tokenUsageService->getTopModelsByCost($userId, $limit, $startDate, $endDate);

        return response()->json([
            'success' => true,
            'data' => $topModels
        ]);
    }

    /**
     * Admin: Get all users usage summary
     */
    public function getAllUsersSummary(Request $request)
    {
        $startDate = $request->has('start_date') ? Carbon::parse($request->start_date) : null;
        $endDate = $request->has('end_date') ? Carbon::parse($request->end_date) : null;

        $summary = $this->tokenUsageService->getAllUsersUsageSummary($startDate, $endDate);

        return response()->json([
            'success' => true,
            'data' => $summary
        ]);
    }
}

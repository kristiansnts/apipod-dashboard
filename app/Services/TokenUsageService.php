<?php

namespace App\Services;

use App\Models\UsageLog;
use App\Models\LlmModel;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TokenUsageService
{
    /**
     * Calculate cost for a single usage log entry
     */
    public function calculateUsageCost(UsageLog $usageLog): float
    {
        $model = LlmModel::where('model_name', $usageLog->routed_model)->first();
        
        if (!$model) {
            return 0.0;
        }

        $inputCost = ($usageLog->input_tokens / 1_000_000) * $model->input_cost_per_1m;
        $outputCost = ($usageLog->output_tokens / 1_000_000) * $model->output_cost_per_1m;

        return $inputCost + $outputCost;
    }

    /**
     * Calculate total cost for a user within a date range
     */
    public function calculateUserCost(int $userId, ?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $query = UsageLog::where('user_id', $userId);

        if ($startDate) {
            $query->where('timestamp', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('timestamp', '<=', $endDate);
        }

        $usageLogs = $query->get();
        $totalCost = 0.0;
        $totalInputTokens = 0;
        $totalOutputTokens = 0;

        foreach ($usageLogs as $log) {
            $totalCost += $this->calculateUsageCost($log);
            $totalInputTokens += $log->input_tokens;
            $totalOutputTokens += $log->output_tokens;
        }

        return [
            'total_cost' => round($totalCost, 4),
            'total_input_tokens' => $totalInputTokens,
            'total_output_tokens' => $totalOutputTokens,
            'total_tokens' => $totalInputTokens + $totalOutputTokens,
            'request_count' => $usageLogs->count(),
        ];
    }

    /**
     * Get usage breakdown by model for a user
     */
    public function getUserUsageByModel(int $userId, ?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $query = UsageLog::select(
            'routed_model',
            DB::raw('SUM(input_tokens) as total_input_tokens'),
            DB::raw('SUM(output_tokens) as total_output_tokens'),
            DB::raw('COUNT(*) as request_count')
        )
        ->where('user_id', $userId)
        ->groupBy('routed_model');

        if ($startDate) {
            $query->where('timestamp', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('timestamp', '<=', $endDate);
        }

        $results = $query->get();
        $breakdown = [];

        foreach ($results as $result) {
            $model = LlmModel::where('model_name', $result->routed_model)->first();
            
            $inputCost = 0.0;
            $outputCost = 0.0;

            if ($model) {
                $inputCost = ($result->total_input_tokens / 1_000_000) * $model->input_cost_per_1m;
                $outputCost = ($result->total_output_tokens / 1_000_000) * $model->output_cost_per_1m;
            }

            $breakdown[] = [
                'model' => $result->routed_model,
                'input_tokens' => $result->total_input_tokens,
                'output_tokens' => $result->total_output_tokens,
                'total_tokens' => $result->total_input_tokens + $result->total_output_tokens,
                'request_count' => $result->request_count,
                'input_cost' => round($inputCost, 4),
                'output_cost' => round($outputCost, 4),
                'total_cost' => round($inputCost + $outputCost, 4),
            ];
        }

        return $breakdown;
    }

    /**
     * Get daily usage analytics for a user
     */
    public function getDailyUsageAnalytics(int $userId, ?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $startDate = $startDate ?? Carbon::now()->subDays(30);
        $endDate = $endDate ?? Carbon::now();

        $query = UsageLog::select(
            DB::raw('DATE(timestamp) as date'),
            DB::raw('SUM(input_tokens) as total_input_tokens'),
            DB::raw('SUM(output_tokens) as total_output_tokens'),
            DB::raw('COUNT(*) as request_count')
        )
        ->where('user_id', $userId)
        ->whereBetween('timestamp', [$startDate, $endDate])
        ->groupBy(DB::raw('DATE(timestamp)'))
        ->orderBy('date', 'asc');

        $results = $query->get();
        $dailyAnalytics = [];

        foreach ($results as $result) {
            // Get all usage logs for this day to calculate accurate costs per model
            $dayUsageLogs = UsageLog::where('user_id', $userId)
                ->whereDate('timestamp', $result->date)
                ->get();

            $dayCost = 0.0;
            foreach ($dayUsageLogs as $log) {
                $dayCost += $this->calculateUsageCost($log);
            }

            $dailyAnalytics[] = [
                'date' => $result->date,
                'input_tokens' => $result->total_input_tokens,
                'output_tokens' => $result->total_output_tokens,
                'total_tokens' => $result->total_input_tokens + $result->total_output_tokens,
                'request_count' => $result->request_count,
                'total_cost' => round($dayCost, 4),
            ];
        }

        return $dailyAnalytics;
    }

    /**
     * Get hourly usage for a specific date
     */
    public function getHourlyUsage(int $userId, Carbon $date): array
    {
        $query = UsageLog::select(
            DB::raw('HOUR(timestamp) as hour'),
            DB::raw('SUM(input_tokens) as total_input_tokens'),
            DB::raw('SUM(output_tokens) as total_output_tokens'),
            DB::raw('COUNT(*) as request_count')
        )
        ->where('user_id', $userId)
        ->whereDate('timestamp', $date)
        ->groupBy(DB::raw('HOUR(timestamp)'))
        ->orderBy('hour', 'asc');

        $results = $query->get();
        $hourlyAnalytics = [];

        foreach ($results as $result) {
            $hourUsageLogs = UsageLog::where('user_id', $userId)
                ->whereDate('timestamp', $date)
                ->whereRaw('HOUR(timestamp) = ?', [$result->hour])
                ->get();

            $hourCost = 0.0;
            foreach ($hourUsageLogs as $log) {
                $hourCost += $this->calculateUsageCost($log);
            }

            $hourlyAnalytics[] = [
                'hour' => str_pad($result->hour, 2, '0', STR_PAD_LEFT) . ':00',
                'input_tokens' => $result->total_input_tokens,
                'output_tokens' => $result->total_output_tokens,
                'total_tokens' => $result->total_input_tokens + $result->total_output_tokens,
                'request_count' => $result->request_count,
                'total_cost' => round($hourCost, 4),
            ];
        }

        return $hourlyAnalytics;
    }

    /**
     * Get top models by cost for a user
     */
    public function getTopModelsByCost(int $userId, int $limit = 5, ?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $breakdown = $this->getUserUsageByModel($userId, $startDate, $endDate);
        
        usort($breakdown, function($a, $b) {
            return $b['total_cost'] <=> $a['total_cost'];
        });

        return array_slice($breakdown, 0, $limit);
    }

    /**
     * Get usage summary for all users (admin view)
     */
    public function getAllUsersUsageSummary(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $users = User::all();
        $summary = [];

        foreach ($users as $user) {
            $userStats = $this->calculateUserCost($user->id, $startDate, $endDate);
            
            if ($userStats['request_count'] > 0) {
                $summary[] = [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_email' => $user->email,
                    'total_cost' => $userStats['total_cost'],
                    'total_tokens' => $userStats['total_tokens'],
                    'request_count' => $userStats['request_count'],
                ];
            }
        }

        usort($summary, function($a, $b) {
            return $b['total_cost'] <=> $a['total_cost'];
        });

        return $summary;
    }
}

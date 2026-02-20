<?php

namespace App\Services;

use App\Models\ApiKey;
use App\Models\Organization;
use App\Models\TokenLedger;
use Illuminate\Support\Facades\DB;

class QuotaEnforcementService
{
    /**
     * Pre-check: soft guard before Go proxy forwards the request.
     * Returns ['allowed' => bool, 'reason' => string|null]
     */
    public function preCheck(int $orgId, int $apiKeyId, string $requestedModel): array
    {
        $org = Organization::with('plan.allowedModels')->find($orgId);

        if (!$org) {
            return ['allowed' => false, 'reason' => 'Organization not found'];
        }

        if (!$org->is_active) {
            return ['allowed' => false, 'reason' => 'Organization is blocked'];
        }

        if (!$org->plan) {
            return ['allowed' => false, 'reason' => 'No active plan'];
        }

        // Balance check: must be > 0
        if ($org->token_balance <= 0) {
            return ['allowed' => false, 'reason' => 'Token quota exceeded'];
        }

        // API key checks
        $apiKey = ApiKey::find($apiKeyId);
        if (!$apiKey || !$apiKey->is_active) {
            return ['allowed' => false, 'reason' => 'API key is inactive or not found'];
        }

        if ($apiKey->org_id !== $orgId) {
            return ['allowed' => false, 'reason' => 'API key does not belong to this organization'];
        }

        // Per-key limit check
        if (!$apiKey->isWithinLimit()) {
            return ['allowed' => false, 'reason' => 'API key token limit exceeded'];
        }

        // Model routing check
        if (!$org->plan->isModelAllowed($requestedModel)) {
            return ['allowed' => false, 'reason' => 'Model not allowed on current plan'];
        }

        return ['allowed' => true, 'reason' => null];
    }

    /**
     * Commit: hard accounting after response. Idempotent via request_id.
     * Called by Go proxy with real token counts.
     *
     * Returns ['success' => bool, 'skipped' => bool, 'reason' => string|null]
     */
    public function commitUsage(
        string $requestId,
        int $orgId,
        ?int $userId,
        ?int $apiKeyId,
        string $model,
        int $inputTokens,
        int $outputTokens,
        float $costUsd
    ): array {
        // Idempotency: skip if request_id already recorded
        if (TokenLedger::where('request_id', $requestId)->exists()) {
            return ['success' => true, 'skipped' => true, 'reason' => 'Already committed'];
        }

        $totalTokens = $inputTokens + $outputTokens;

        try {
            DB::transaction(function () use ($requestId, $orgId, $userId, $apiKeyId, $model, $inputTokens, $outputTokens, $costUsd, $totalTokens) {
                // Lock org row to prevent race conditions
                $org = Organization::lockForUpdate()->findOrFail($orgId);

                // Deduct real tokens from org balance
                $org->decrement('token_balance', $totalTokens);
                $org->refresh();

                // Per-key tracking
                if ($apiKeyId) {
                    ApiKey::where('id', $apiKeyId)->increment('used_tokens', $totalTokens);
                    ApiKey::where('id', $apiKeyId)->update(['last_used_at' => now()]);
                }

                // Record ledger entry
                TokenLedger::create([
                    'org_id' => $orgId,
                    'user_id' => $userId,
                    'api_key_id' => $apiKeyId,
                    'request_id' => $requestId,
                    'type' => 'usage',
                    'model' => $model,
                    'input_tokens' => $inputTokens,
                    'output_tokens' => $outputTokens,
                    'cost_usd' => $costUsd,
                    'balance_after' => $org->token_balance,
                ]);
            });

            return ['success' => true, 'skipped' => false, 'reason' => null];
        } catch (\Exception $e) {
            return ['success' => false, 'skipped' => false, 'reason' => $e->getMessage()];
        }
    }

    /**
     * Record a non-usage ledger entry (topup, adjustment).
     */
    public function recordTopup(int $orgId, int $tokenAmount, string $description = 'Token topup'): void
    {
        DB::transaction(function () use ($orgId, $tokenAmount, $description) {
            $org = Organization::lockForUpdate()->findOrFail($orgId);
            $org->increment('token_balance', $tokenAmount);
            $org->refresh();

            TokenLedger::create([
                'org_id' => $orgId,
                'type' => 'topup',
                'input_tokens' => 0,
                'output_tokens' => 0,
                'cost_usd' => 0,
                'balance_after' => $org->token_balance,
                'description' => $description,
            ]);
        });
    }

    /**
     * Get margin report for admin: total cost vs revenue.
     */
    public function getMarginReport(?\Carbon\Carbon $startDate = null, ?\Carbon\Carbon $endDate = null): array
    {
        $query = TokenLedger::where('type', 'usage');

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        $totalCostUsd = $query->sum('cost_usd');
        $totalInputTokens = $query->sum('input_tokens');
        $totalOutputTokens = $query->sum('output_tokens');

        return [
            'total_cost_usd' => (float) $totalCostUsd,
            'total_input_tokens' => (int) $totalInputTokens,
            'total_output_tokens' => (int) $totalOutputTokens,
            'total_tokens' => (int) ($totalInputTokens + $totalOutputTokens),
        ];
    }
}

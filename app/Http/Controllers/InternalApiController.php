<?php

namespace App\Http\Controllers;

use App\Models\ApiKey;
use App\Models\Organization;
use App\Models\OrgProviderKey;
use App\Models\TokenLedger;
use App\Models\LlmModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InternalApiController extends Controller
{
    /**
     * GET /internal/runtime-config?api_key=XXX
     *
     * Called by the Go proxy to get everything it needs for a request.
     * Returns: mode, upstream_key, rate limits, allowed models, daily counters.
     */
    public function runtimeConfig(Request $request): JsonResponse
    {
        $plainKey = $request->query('api_key');

        if (empty($plainKey)) {
            return response()->json(['allowed' => false, 'reason' => 'Missing api_key'], 401);
        }

        // Find the API key by hash
        $apiKey = ApiKey::findByPlainKey($plainKey);

        if (!$apiKey || !$apiKey->is_active) {
            return response()->json(['allowed' => false, 'reason' => 'Invalid or revoked API key'], 401);
        }

        $org = $apiKey->organization;

        if (!$org || !$org->is_active) {
            return response()->json(['allowed' => false, 'reason' => 'Organization suspended'], 403);
        }

        $plan = $org->plan;

        if (!$plan) {
            return response()->json(['allowed' => false, 'reason' => 'No active plan'], 403);
        }

        // Reset daily counter if date changed
        $today = now()->toDateString();
        if ($org->daily_request_date !== $today) {
            $org->update([
                'daily_request_count' => 0,
                'daily_request_date' => $today,
            ]);
            $org->refresh();
        }

        // Build allowed models list
        $allowedModels = $plan->allowedModels()->pluck('model_name')->toArray();

        // Determine mode
        $isByok = (bool) $plan->is_byok;
        $mode = $isByok ? 'byok' : 'platform';

        // For BYOK: find user's upstream keys (provider_id → key mapping)
        $upstreamKeys = [];
        if ($isByok) {
            $orgKeys = OrgProviderKey::where('org_id', $org->id)
                ->where('is_active', true)
                ->with(['provider.llmModels'])
                ->get();

            foreach ($orgKeys as $orgKey) {
                $upstreamKeys[] = [
                    'provider_id' => $orgKey->provider_id,
                    'provider_type' => $orgKey->provider->provider_type ?? '',
                    'provider_name' => $orgKey->provider->name ?? '',
                    'base_url' => $orgKey->provider->base_url ?? '',
                    'api_key' => $orgKey->api_key, // decrypted by Laravel cast
                    'models' => $orgKey->provider->llmModels->pluck('model_name')->values()->toArray(),
                ];
            }
        }

        // For platform mode: check token balance
        if (!$isByok && $org->token_balance <= 0) {
            return response()->json(['allowed' => false, 'reason' => 'Token quota exceeded'], 429);
        }

        // Per-key limit check
        if (!$apiKey->isWithinLimit()) {
            return response()->json(['allowed' => false, 'reason' => 'API key token limit exceeded'], 429);
        }

        // For BYOK: get the user's selected active model
        $activeModelConfig = null;
        if ($isByok && $org->active_model_id) {
            $activeModel = $org->activeModel;
            if ($activeModel) {
                // Find the upstream key for this model's provider
                $upstreamKey = collect($upstreamKeys)->firstWhere('provider_id', $activeModel->provider_id);
                $activeModelConfig = [
                    'model_name' => $activeModel->model_name,
                    'provider_id' => $activeModel->provider_id,
                    'provider_type' => $upstreamKey['provider_type'] ?? '',
                    'base_url' => $upstreamKey['base_url'] ?? '',
                    'api_key' => $upstreamKey['api_key'] ?? null,
                ];
            }
        }

        return response()->json([
            'allowed' => true,
            'mode' => $mode,
            'org_id' => $org->id,
            'api_key_id' => $apiKey->id,
            'sub_id' => $plan->sub_id,
            'rate_limit_rpm' => $plan->rate_limit_rpm ?? ($isByok ? 30 : 300),
            'daily_quota' => $plan->daily_request_cap ?? ($isByok ? 1000 : 0),
            'daily_used' => $org->daily_request_count,
            'allowed_models' => $allowedModels,
            'priority' => $isByok ? 'low' : 'normal',
            'upstream_keys' => $isByok ? $upstreamKeys : [],
            'token_balance' => $isByok ? null : $org->token_balance,
            'active_model' => $activeModelConfig,
        ]);
    }

    /**
     * POST /internal/commit-usage
     *
     * Called by Go proxy AFTER response, async. Records usage.
     * For BYOK: analytics only, no token deduction.
     * For platform: deduct tokens + record ledger.
     */
    public function commitUsage(Request $request): JsonResponse
    {
        $request->validate([
            'request_id' => 'required|string|max:255',
            'org_id' => 'required|integer',
            'api_key_id' => 'nullable|integer',
            'model' => 'required|string',
            'input_tokens' => 'required|integer|min:0',
            'output_tokens' => 'required|integer|min:0',
            'mode' => 'required|string|in:byok,platform',
            'status_code' => 'nullable|integer',
            'latency_ms' => 'nullable|integer|min:0',
            'cache_hit' => 'nullable|boolean',
        ]);

        // Idempotency check
        if (TokenLedger::where('request_id', $request->input('request_id'))->exists()) {
            return response()->json(['success' => true, 'skipped' => true]);
        }

        $mode = $request->input('mode');
        $orgId = $request->input('org_id');
        $totalTokens = $request->input('input_tokens') + $request->input('output_tokens');

        // Calculate cost (for both modes — analytics)
        $costUsd = 0;
        $model = LlmModel::where('model_name', $request->input('model'))->first();
        if ($model) {
            $costUsd = ($request->input('input_tokens') / 1_000_000) * (float) $model->input_cost_per_1m
                + ($request->input('output_tokens') / 1_000_000) * (float) $model->output_cost_per_1m;
        }

        try {
            DB::transaction(function () use ($request, $mode, $orgId, $totalTokens, $costUsd) {
                $org = Organization::lockForUpdate()->findOrFail($orgId);

                if ($mode === 'platform') {
                    // Deduct tokens
                    $org->decrement('token_balance', $totalTokens);
                    $org->refresh();

                    // Update per-key usage
                    if ($request->input('api_key_id')) {
                        ApiKey::where('id', $request->input('api_key_id'))
                            ->increment('used_tokens', $totalTokens);
                        ApiKey::where('id', $request->input('api_key_id'))
                            ->update(['last_used_at' => now()]);
                    }
                }

                // Increment daily request count (both modes)
                $org->increment('daily_request_count');

                // Record ledger
                TokenLedger::create([
                    'org_id' => $orgId,
                    'api_key_id' => $request->input('api_key_id'),
                    'request_id' => $request->input('request_id'),
                    'type' => $mode === 'byok' ? 'byok_usage' : 'usage',
                    'model' => $request->input('model'),
                    'input_tokens' => $request->input('input_tokens'),
                    'output_tokens' => $request->input('output_tokens'),
                    'cost_usd' => $costUsd,
                    'balance_after' => $org->token_balance,
                    'status_code' => $request->input('status_code', 200),
                    'latency_ms' => $request->input('latency_ms', 0),
                    'cache_hit' => (bool) $request->input('cache_hit', false),
                ]);
            });

            return response()->json(['success' => true, 'skipped' => false]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'reason' => $e->getMessage()], 500);
        }
    }
}

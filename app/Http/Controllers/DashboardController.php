<?php

namespace App\Http\Controllers;

use App\Models\LlmModel;
use App\Models\ApiKey;
use App\Models\OrgProviderKey;
use App\Models\Provider;
use App\Models\QuotaItem;
use App\Models\TokenLedger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $org = $user->organization;
        $plan = $org?->plan;

        $quotaPercent = 0;
        if ($plan && $plan->token_quota > 0) {
            $quotaPercent = round(($org->token_balance / $plan->token_quota) * 100, 1);
            $quotaPercent = max(0, min(100, $quotaPercent));
        }

        $ledgerEntries = $org
            ? $org->tokenLedger()->orderBy('created_at', 'desc')->paginate(10)
            : collect();

        $dailyRequestCount = $org
            ? $org->tokenLedger()->whereDate('created_at', now()->toDateString())->where('type', 'like', '%usage%')->count()
            : 0;

        return view('home', compact('user', 'org', 'plan', 'quotaPercent', 'ledgerEntries', 'dailyRequestCount'));
    }

    public function quickstart()
    {
        return view('dashboard.quickstart');
    }

    public function models()
    {
        $models = LlmModel::with('provider')->get();
        return view('dashboard.models', compact('models'));
    }

    public function usage()
    {
        $user = auth()->user();
        $org = $user->organization;

        $ledgerEntries = $org
            ? $org->tokenLedger()->orderBy('created_at', 'desc')->paginate(15)
            : collect();

        return view('dashboard.usage', compact('user', 'org', 'ledgerEntries'));
    }

    public function apiKeys()
    {
        $user = auth()->user();
        $org = $user->organization;
        $apiKeys = $org ? $org->apiKeys()->orderBy('created_at', 'desc')->get() : collect();

        return view('dashboard.api-keys', compact('user', 'org', 'apiKeys'));
    }

    public function createApiKey(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'token_limit' => 'nullable|integer|min:1',
        ]);

        $user = auth()->user();
        $org = $user->organization;

        if (!$org) {
            return back()->with('error', 'No organization found.');
        }

        $result = ApiKey::generateKey(
            $org->id,
            $request->input('name'),
            $request->input('token_limit'),
        );

        return back()->with('success', 'API Key created successfully!')
            ->with('new_key', $result['plain_key']);
    }

    public function revokeApiKey(ApiKey $apiKey)
    {
        $user = auth()->user();

        if ($apiKey->org_id !== $user->org_id) {
            abort(403);
        }

        $apiKey->update(['is_active' => false]);

        return back()->with('success', 'API Key revoked.');
    }

    public function planStatus()
    {
        $user = auth()->user();
        $org = $user->organization;
        $plan = $org?->plan;

        return view('dashboard.plan-status', compact('user', 'org', 'plan'));
    }

    public function providerKeys()
    {
        $user = auth()->user();
        $org = $user->organization;
        $plan = $org?->plan;

        // Gate: only BYOK plan users
        if (!$plan || !$plan->is_byok) {
            return redirect()->route('home')->with('error', 'Provider keys are only available on BYOK plans.');
        }

        $keys = $org->providerKeys()->with('provider')->get();

        // Only show providers that have models allowed by the plan
        $allowedModels = $plan->allowedModels;
        if ($allowedModels->isNotEmpty()) {
            // Filter by providers with allowed models
            $allowedProviderIds = $allowedModels->pluck('provider_id')->unique()->toArray();
            $providers = Provider::where('is_active', true)
                ->whereIn('id', $allowedProviderIds)
                ->get();
        } else {
            // BYOK plans: only show providers that support user-provided keys
            // Other providers (Antigravity, CCS, etc.) use platform-managed keys
            $byokProviderNames = ['Nvidia NIM', 'Openrouter'];
            $providers = Provider::where('is_active', true)
                ->whereIn('name', $byokProviderNames)
                ->get();
        }

        $usedProviderIds = $keys->pluck('provider_id')->toArray();

        // Get allowed models grouped by provider for display
        $modelsByProvider = $allowedModels->groupBy('provider_id');

        // Get available models for model selection (from providers user has keys for)
        $activeProviderIds = $keys->where('is_active', true)->pluck('provider_id')->toArray();
        $availableModels = LlmModel::whereIn('provider_id', $activeProviderIds)
            ->with('provider')
            ->orderBy('model_name')
            ->get();

        $activeModel = $org->activeModel;

        return view('dashboard.provider-keys', compact('user', 'org', 'keys', 'providers', 'usedProviderIds', 'modelsByProvider', 'availableModels', 'activeModel'));
    }

    public function selectModel(Request $request)
    {
        $request->validate([
            'model_id' => 'required|exists:llm_models,llm_model_id',
        ]);

        $user = auth()->user();
        $org = $user->organization;

        if (!$org?->plan?->is_byok) {
            return back()->with('error', 'Model selection is only available on BYOK plans.');
        }

        // Verify model belongs to a provider the user has a key for
        $model = LlmModel::findOrFail($request->input('model_id'));
        $hasKey = $org->providerKeys()
            ->where('provider_id', $model->provider_id)
            ->where('is_active', true)
            ->exists();

        if (!$hasKey) {
            return back()->with('error', 'Add a provider key for this model\'s provider first.');
        }

        $org->update(['active_model_id' => $model->llm_model_id]);

        return back()->with('success', 'Active model set to: ' . $model->model_name);
    }

    public function storeProviderKey(Request $request)
    {
        $request->validate([
            'provider_id' => 'required|exists:providers,id',
            'api_key' => 'required|string|min:10',
            'label' => 'required|string|max:255',
        ]);

        $user = auth()->user();
        $org = $user->organization;

        if (!$org?->plan?->is_byok) {
            return back()->with('error', 'Provider keys are only available on BYOK plans.');
        }

        // Check if key already exists for this provider
        $existing = OrgProviderKey::where('org_id', $org->id)
            ->where('provider_id', $request->input('provider_id'))
            ->first();

        if ($existing) {
            // Update existing key
            $existing->update([
                'api_key' => $request->input('api_key'),
                'label' => $request->input('label'),
                'is_active' => true,
            ]);
            return back()->with('success', 'Provider key updated.');
        }

        OrgProviderKey::create([
            'org_id' => $org->id,
            'provider_id' => $request->input('provider_id'),
            'api_key' => $request->input('api_key'),
            'label' => $request->input('label'),
        ]);

        return back()->with('success', 'Provider key added.');
    }

    public function deleteProviderKey(OrgProviderKey $providerKey)
    {
        $user = auth()->user();

        if ($providerKey->org_id !== $user->org_id) {
            abort(403);
        }

        $providerKey->delete();

        return back()->with('success', 'Provider key removed.');
    }

    public function modelWeights()
    {
        $user = auth()->user();
        $org = $user->organization;
        $plan = $org?->plan;

        if (!$plan || $plan->is_byok) {
            return redirect()->route('home')->with('error', 'Model weights are only available on platform plans.');
        }

        $subId = $plan->sub_id;

        // Load existing quota items
        $quotaItems = QuotaItem::where('sub_id', $subId)
            ->with('llmModel.provider')
            ->get();

        // Auto-initialize if no quota items exist
        if ($quotaItems->isEmpty()) {
            $allowedModels = $plan->allowedModels;
            if ($allowedModels->isNotEmpty()) {
                $count = $allowedModels->count();
                $baseWeight = intdiv(100, $count);
                $remainder = 100 - ($baseWeight * $count);

                foreach ($allowedModels->values() as $i => $model) {
                    $weight = $model->default_weight ?? $baseWeight;
                    if ($i === 0) {
                        $weight += $remainder;
                    }
                    QuotaItem::create([
                        'sub_id' => $subId,
                        'llm_model_id' => $model->llm_model_id,
                        'percentage_weight' => $weight,
                    ]);
                }

                $quotaItems = QuotaItem::where('sub_id', $subId)
                    ->with('llmModel.provider')
                    ->get();
            }
        }

        return view('dashboard.model-weights', compact('user', 'org', 'plan', 'quotaItems'));
    }

    public function updateModelWeights(Request $request)
    {
        $user = auth()->user();
        $org = $user->organization;
        $plan = $org?->plan;

        if (!$plan || $plan->is_byok) {
            return back()->with('error', 'Model weights are only available on platform plans.');
        }

        $request->validate([
            'weights' => 'required|array',
            'weights.*' => 'required|integer|min:0|max:100',
        ]);

        $weights = $request->input('weights');

        if (array_sum($weights) !== 100) {
            return back()->with('error', 'Weights must add up to 100%.')->withInput();
        }

        $subId = $plan->sub_id;

        foreach ($weights as $quotaId => $weight) {
            QuotaItem::where('quota_id', $quotaId)
                ->where('sub_id', $subId)
                ->update(['percentage_weight' => $weight]);
        }

        return back()->with('success', 'Model weights updated successfully.');
    }

    public function analytics()
    {
        $user = auth()->user();
        $org = $user->organization;
        $orgId = $org?->id;

        $successRate  = null;
        $cacheHitRate = null;
        $avgLatency   = null;
        $p95Latency   = null;
        $totalRequests = 0;
        $totalTokens   = 0;
        $totalCost     = 0;
        $modelDistribution = collect();
        $recentLogs = collect();

        if ($orgId) {
            $ledgerQuery = DB::table('token_ledger')
                ->where('org_id', $orgId)
                ->whereIn('type', ['usage', 'byok_usage']);

            $stats = (clone $ledgerQuery)->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status_code < 400 THEN 1 ELSE 0 END) as successes,
                SUM(CASE WHEN cache_hit = true THEN 1 ELSE 0 END) as cache_hits,
                AVG(CASE WHEN cache_hit = false THEN latency_ms END) as avg_latency,
                SUM(input_tokens + output_tokens) as total_tokens,
                SUM(cost_usd) as total_cost
            ')->first();

            if ($stats && $stats->total > 0) {
                $totalRequests = $stats->total;
                $totalTokens   = (int) $stats->total_tokens;
                $totalCost     = (float) $stats->total_cost;
                $successRate   = round(($stats->successes / $stats->total) * 100, 1);
                $cacheHitRate  = round(($stats->cache_hits / $stats->total) * 100, 1);
                $avgLatency    = $stats->avg_latency !== null ? (int) round($stats->avg_latency) : null;

                $latencies = (clone $ledgerQuery)
                    ->where('cache_hit', false)
                    ->whereNotNull('latency_ms')
                    ->orderBy('latency_ms')
                    ->pluck('latency_ms');

                if ($latencies->isNotEmpty()) {
                    $idx = (int) ceil(0.95 * $latencies->count()) - 1;
                    $p95Latency = $latencies->values()[$idx];
                }
            }

            // Model distribution
            $modelDistribution = (clone $ledgerQuery)
                ->select('model')
                ->selectRaw('COUNT(*) as count')
                ->selectRaw('SUM(input_tokens + output_tokens) as tokens')
                ->selectRaw('SUM(cost_usd) as cost')
                ->groupBy('model')
                ->orderByDesc('count')
                ->get();

            // Recent logs from token_ledger
            $recentLogs = $org->tokenLedger()
                ->whereIn('type', ['usage', 'byok_usage'])
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get();
        }

        return view('dashboard.analytics', compact(
            'successRate', 'cacheHitRate', 'avgLatency', 'p95Latency',
            'totalRequests', 'totalTokens', 'totalCost',
            'modelDistribution', 'recentLogs'
        ));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\LlmModel;
use App\Models\ApiKey;
use App\Models\OrgProviderKey;
use App\Models\Provider;
use Illuminate\Http\Request;

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

        return view('home', compact('user', 'org', 'plan', 'quotaPercent'));
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

        // Show ledger entries for the org
        $ledgerEntries = $org
            ? $org->tokenLedger()->orderBy('created_at', 'desc')->paginate(15)
            : collect();

        $usageLogs = $user->usageLogs()->orderBy('usage_id', 'desc')->paginate(15);

        return view('dashboard.usage', compact('usageLogs', 'user', 'org', 'ledgerEntries'));
    }

    public function apiKeys()
    {
        $user = auth()->user();
        $org = $user->organization;
        $apiKeys = $org ? $org->apiKeys()->orderBy('created_at', 'desc')->get() : collect();
        $canCreate = $org ? $org->canCreateApiKey() : false;
        $maxKeys = $org?->plan?->max_api_keys ?? 0;

        return view('dashboard.api-keys', compact('user', 'org', 'apiKeys', 'canCreate', 'maxKeys'));
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

        if (!$org->canCreateApiKey()) {
            return back()->with('error', "Maximum API keys ({$org->plan->max_api_keys}) reached for your plan.");
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

    public function analytics()
    {
        $user = auth()->user();
        $logs = $user->usageLogs()->orderBy('usage_id', 'desc')->get();
        return view('dashboard.analytics', compact('logs'));
    }
}

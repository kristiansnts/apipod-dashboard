<?php

namespace App\Http\Controllers;

use App\Models\LlmModel;
use App\Models\ApiKey;
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

    public function analytics()
    {
        $user = auth()->user();
        $logs = $user->usageLogs()->orderBy('usage_id', 'desc')->get();
        return view('dashboard.analytics', compact('logs'));
    }
}

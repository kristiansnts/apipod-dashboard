<?php

namespace App\Http\Controllers;

use App\Models\ApiKey;
use App\Models\LlmModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CliApiController extends Controller
{
    /**
     * GET /api/cli/setup
     *
     * Called by apipod CLI after device auth to get models and provider info
     * needed to generate a CCR config. Authenticated via API key (Bearer token).
     */
    public function setup(Request $request): JsonResponse
    {
        $plainKey = $request->bearerToken();

        if (empty($plainKey)) {
            return response()->json(['error' => 'Missing API key'], 401);
        }

        $apiKey = ApiKey::findByPlainKey($plainKey);

        if (!$apiKey || !$apiKey->is_active) {
            return response()->json(['error' => 'Invalid or revoked API key'], 401);
        }

        $org = $apiKey->organization;

        if (!$org || !$org->is_active) {
            return response()->json(['error' => 'Organization suspended'], 403);
        }

        $plan = $org->plan;

        if (!$plan) {
            return response()->json(['error' => 'No active plan'], 403);
        }

        // BYOK plan: return only the model the org has selected in the dashboard
        if ($plan->is_byok) {
            $activeModel = $org->activeModel;

            if (!$activeModel) {
                return response()->json([
                    'plan' => $plan->name,
                    'is_byok' => true,
                    'providers' => [],
                    'message' => 'No model selected. Choose a model in your dashboard.',
                ]);
            }

            $orgKey = $org->providerKeys()
                ->where('provider_id', $activeModel->provider_id)
                ->where('is_active', true)
                ->with('provider')
                ->first();

            if (!$orgKey) {
                return response()->json([
                    'plan' => $plan->name,
                    'is_byok' => true,
                    'providers' => [],
                    'message' => 'No API key configured for your selected model\'s provider. Add one in your dashboard.',
                ]);
            }

            return response()->json([
                'plan' => $plan->name,
                'is_byok' => true,
                'providers' => [[
                    'name' => $orgKey->provider->name,
                    'provider_type' => $orgKey->provider->provider_type,
                    'base_url' => $orgKey->provider->base_url,
                    'models' => [$activeModel->model_name],
                ]],
            ]);
        }

        // Get allowed models with provider info
        $allowedModels = $plan->allowedModels()
            ->with('provider')
            ->get()
            ->map(fn($model) => [
                'model_name' => $model->model_name,
                'provider_name' => $model->provider->name ?? '',
                'provider_type' => $model->provider->provider_type ?? '',
                'base_url' => $model->provider->base_url ?? '',
            ]);

        // Group models by provider for CCR config generation
        $providers = $allowedModels->groupBy('provider_name')->map(function ($models, $providerName) {
            $first = $models->first();
            return [
                'name' => $providerName,
                'provider_type' => $first['provider_type'],
                'base_url' => $first['base_url'],
                'models' => $models->pluck('model_name')->values()->toArray(),
            ];
        })->values();

        return response()->json([
            'plan' => $plan->name,
            'is_byok' => (bool) $plan->is_byok,
            'providers' => $providers,
        ]);
    }
}

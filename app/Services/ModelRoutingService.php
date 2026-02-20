<?php

namespace App\Services;

use App\Models\Organization;

class ModelRoutingService
{
    /**
     * Check if the requested model is allowed for the organization's plan.
     *
     * @return array ['allowed' => bool, 'reason' => string|null]
     */
    public function checkModelAccess(int $orgId, string $requestedModel): array
    {
        $org = Organization::with('plan.allowedModels')->find($orgId);

        if (!$org) {
            return ['allowed' => false, 'reason' => 'Organization not found'];
        }

        if (!$org->plan) {
            return ['allowed' => false, 'reason' => 'No active plan'];
        }

        // Check if the plan has any model restrictions
        $allowedModels = $org->plan->allowedModels;

        if ($allowedModels->isEmpty()) {
            // No restrictions defined → allow all (BYOK / Free plan behavior)
            return ['allowed' => true, 'reason' => null];
        }

        // Check if requested model is in the allowed list
        $isAllowed = $allowedModels->contains('model_name', $requestedModel);

        if (!$isAllowed) {
            return [
                'allowed' => false,
                'reason' => "Model '{$requestedModel}' is not available on the '{$org->plan->name}' plan",
            ];
        }

        // Check if the provider for this model is active
        $model = $allowedModels->firstWhere('model_name', $requestedModel);
        if ($model && $model->provider && !$model->provider->is_active) {
            return [
                'allowed' => false,
                'reason' => "Provider for '{$requestedModel}' is currently disabled",
            ];
        }

        return ['allowed' => true, 'reason' => null];
    }

    /**
     * Get the list of models available for an organization.
     */
    public function getAvailableModels(int $orgId): array
    {
        $org = Organization::with('plan.allowedModels.provider')->find($orgId);

        if (!$org || !$org->plan) {
            return [];
        }

        $allowedModels = $org->plan->allowedModels;

        if ($allowedModels->isEmpty()) {
            // No restrictions → return all active models
            return \App\Models\LlmModel::whereHas('provider', function ($q) {
                $q->where('is_active', true);
            })->get()->toArray();
        }

        return $allowedModels->filter(function ($model) {
            return $model->provider && $model->provider->is_active;
        })->values()->toArray();
    }
}

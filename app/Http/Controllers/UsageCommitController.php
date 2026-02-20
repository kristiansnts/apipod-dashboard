<?php

namespace App\Http\Controllers;

use App\Services\QuotaEnforcementService;
use App\Services\ModelRoutingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UsageCommitController extends Controller
{
    public function __construct(
        private QuotaEnforcementService $quotaService,
        private ModelRoutingService $routingService,
    ) {
    }

    /**
     * Pre-check: soft guard before Go proxy forwards request.
     * Called BY the Go proxy before forwarding to upstream provider.
     */
    public function preCheck(Request $request): JsonResponse
    {
        $request->validate([
            'org_id' => 'required|integer',
            'api_key_id' => 'required|integer',
            'model' => 'required|string',
        ]);

        $result = $this->quotaService->preCheck(
            $request->input('org_id'),
            $request->input('api_key_id'),
            $request->input('model'),
        );

        if (!$result['allowed']) {
            return response()->json([
                'allowed' => false,
                'reason' => $result['reason'],
            ], 429);
        }

        return response()->json(['allowed' => true]);
    }

    /**
     * Commit: hard accounting after response from upstream provider.
     * Called BY the Go proxy after receiving response with real token counts.
     * Idempotent via request_id.
     */
    public function commit(Request $request): JsonResponse
    {
        $request->validate([
            'request_id' => 'required|string|max:255',
            'org_id' => 'required|integer',
            'user_id' => 'nullable|integer',
            'api_key_id' => 'nullable|integer',
            'model' => 'required|string',
            'input_tokens' => 'required|integer|min:0',
            'output_tokens' => 'required|integer|min:0',
            'cost_usd' => 'required|numeric|min:0',
        ]);

        $result = $this->quotaService->commitUsage(
            $request->input('request_id'),
            $request->input('org_id'),
            $request->input('user_id'),
            $request->input('api_key_id'),
            $request->input('model'),
            $request->input('input_tokens'),
            $request->input('output_tokens'),
            $request->input('cost_usd'),
        );

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'reason' => $result['reason'],
            ], 500);
        }

        return response()->json([
            'success' => true,
            'skipped' => $result['skipped'],
        ]);
    }
}

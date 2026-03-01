<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InternalApiSecret
{
    /**
     * Protect internal API endpoints with a shared secret.
     * The Go proxy must send X-Internal-Secret header matching INTERNAL_API_SECRET env var.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $secret = config('services.internal.api_secret');

        if (empty($secret)) {
            return response()->json(['error' => 'Internal API not configured'], 500);
        }

        $provided = $request->header('X-Internal-Secret');

        if ($provided !== $secret) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}

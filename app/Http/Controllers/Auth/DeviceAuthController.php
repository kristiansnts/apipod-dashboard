<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class DeviceAuthController extends Controller
{
    public function show()
    {
        return view('auth.device');
    }

    public function approveDevice(Request $request)
    {
        $request->validate([
            'user_code' => 'required|string|max:20',
        ]);

        $user = Auth::user();

        if (!$user->apitoken) {
            return back()->withErrors(['user_code' => 'You do not have an API token. Please purchase a plan first.']);
        }

        $proxyUrl = config('services.apipod_proxy.url', 'http://localhost:8081');

        try {
            $response = Http::post($proxyUrl . '/auth/device/authorize', [
                'user_code' => strtoupper(str_replace(' ', '', $request->user_code)),
                'user_id'   => $user->id,
                'api_token' => $user->apitoken,
                'username'  => $user->name,
                'plan'      => $user->plan?->sub_name ?? 'free',
            ]);

            if ($response->successful()) {
                return back()->with('success', 'Device authorized! You can close this page and return to your terminal.');
            }

            $error = $response->json('error', 'Invalid or expired device code. Please try again.');
            return back()->withErrors(['user_code' => $error])->withInput();
        } catch (\Exception $e) {
            return back()->withErrors(['user_code' => 'Could not connect to the API server. Please try again later.'])->withInput();
        }
    }
}

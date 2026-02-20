<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ApiKey;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class DeviceAuthController extends Controller
{
    private const TTL_MINUTES = 10;
    private const POLL_INTERVAL = 5;

    public function show()
    {
        return view('auth.device');
    }

    /**
     * POST /auth/device/code — CLI requests a device code.
     */
    public function requestCode(Request $request)
    {
        $deviceCode = bin2hex(random_bytes(20));
        $userCode = $this->generateUserCode();

        $data = [
            'device_code' => $deviceCode,
            'user_code' => $userCode,
            'status' => 'pending',
            'created_at' => now()->toIso8601String(),
        ];

        Cache::put("device_auth:{$deviceCode}", $data, now()->addMinutes(self::TTL_MINUTES));
        Cache::put("device_auth_user:{$userCode}", $deviceCode, now()->addMinutes(self::TTL_MINUTES));

        return response()->json([
            'device_code' => $deviceCode,
            'user_code' => $userCode,
            'verification_url' => config('app.url') . '/auth/device',
            'interval' => self::POLL_INTERVAL,
            'expires_in' => self::TTL_MINUTES * 60,
        ]);
    }

    /**
     * POST /auth/device/token — CLI polls for authorization result.
     */
    public function pollToken(Request $request)
    {
        $request->validate(['device_code' => 'required|string']);

        $data = Cache::get("device_auth:{$request->device_code}");

        if (!$data) {
            return response()->json(['error' => 'invalid device code'], 404);
        }

        $resp = ['status' => $data['status']];

        if ($data['status'] === 'authorized') {
            $resp['api_token'] = $data['api_token'];
            $resp['username'] = $data['username'];
            $resp['plan'] = $data['plan'];
            $resp['active_model'] = $data['active_model'] ?? null;
            $resp['is_byok'] = $data['is_byok'] ?? false;
        }

        return response()->json($resp);
    }

    /**
     * POST /auth/device/authorize — Dashboard user approves device (web, requires auth).
     */
    public function approveDevice(Request $request)
    {
        $request->validate([
            'user_code' => 'required|string|max:20',
        ]);

        $user = Auth::user();

        if (!$user->apitoken) {
            return back()->withErrors(['user_code' => 'You do not have an API token. Please purchase a plan first.']);
        }

        $normalizedCode = strtoupper(str_replace([' ', '-'], '', $request->user_code));
        $normalizedCode = substr($normalizedCode, 0, 4) . '-' . substr($normalizedCode, 4);

        $deviceCode = Cache::get("device_auth_user:{$normalizedCode}");

        if (!$deviceCode) {
            return back()->withErrors(['user_code' => 'Invalid or expired device code. Please try again.'])->withInput();
        }

        $data = Cache::get("device_auth:{$deviceCode}");

        if (!$data) {
            return back()->withErrors(['user_code' => 'Invalid or expired device code. Please try again.'])->withInput();
        }

        $data['status'] = 'authorized';

        // Generate a new secure API key for this device
        $result = ApiKey::generateKey($user->org_id, 'CLI: ' . ($request->header('User-Agent') ?? 'Unknown Device'));

        $data['api_token'] = $result['plain_key'];
        $data['username'] = $user->name;
        $data['plan'] = $user->plan?->sub_name ?? 'free';
        $data['active_model'] = $user->organization?->activeModel?->model_name;
        $data['is_byok'] = $user->plan?->is_byok ?? false;

        Cache::put("device_auth:{$deviceCode}", $data, now()->addMinutes(self::TTL_MINUTES));

        return redirect()->route('device.success');
    }

    private function generateUserCode(): string
    {
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $part1 = '';
        $part2 = '';
        for ($i = 0; $i < 4; $i++) {
            $part1 .= $chars[random_int(0, strlen($chars) - 1)];
            $part2 .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $part1 . '-' . $part2;
    }
}

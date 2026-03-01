<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Models\Organization;
use App\Models\Plan;
use Illuminate\Support\Str;

class SocialiteController extends Controller
{
    public function handleProviderCallback(Request $request, $provider)
    {
        // Use stateless() to avoid session state mismatch errors on localhost
        $socialUser = Socialite::driver($provider)->stateless()->user();

        $user = User::where('email', $socialUser->getEmail())->first();

        if (! $user) {
            $name = $socialUser->getName() ?? $socialUser->getNickname() ?? $socialUser->getEmail();

            $freePlan = Plan::freePlan();

            $org = Organization::create([
                'name' => $name,
                'slug' => Str::slug($name) . '-' . Str::random(6),
                'is_active' => true,
                'token_balance' => $freePlan?->token_quota ?? 0,
                'plan_id' => $freePlan?->id,
            ]);

            $user = User::create([
                'name' => $name,
                'email' => $socialUser->getEmail(),
                'provider_name' => $provider,
                'provider_id' => $socialUser->getId(),
                'password' => null,
                'org_id' => $org->id,
                'role' => 'owner',
            ]);
        } else {
            // Update existing user with provider info if not already set
            $user->update([
                'provider_name' => $provider,
                'provider_id' => $socialUser->getId(),
            ]);
        }

        Auth::login($user, true);

        return redirect()->route('home');
    }
}

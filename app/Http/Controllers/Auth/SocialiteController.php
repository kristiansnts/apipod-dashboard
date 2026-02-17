<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;

class SocialiteController extends Controller
{
    public function handleProviderCallback(Request $request, $provider)
    {
        // Use stateless() to avoid session state mismatch errors on localhost
        $socialUser = Socialite::driver($provider)->stateless()->user();

        $user = User::where('email', $socialUser->getEmail())->first();

        if (! $user) {
            $user = User::create([
                'name' => $socialUser->getName() ?? $socialUser->getNickname() ?? $socialUser->getEmail(),
                'email' => $socialUser->getEmail(),
                'provider_name' => $provider,
                'provider_id' => $socialUser->getId(),
                'password' => null, // Password is not required for OAuth users
            ]);
        } else {
            // Update existing user with provider info if not already set
            $user->update([
                'provider_name' => $provider,
                'provider_id' => $socialUser->getId(),
            ]);
        }

        Auth::login($user, true);

        return redirect()->route('dashboard');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\SocialProvider;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    private function set_credentials(string $provider_name)
    {
        $social_provider = SocialProvider::where('name', $provider_name)->firstOrFail();

        config([
            sprintf('services.%s.client_id', $provider_name) => Crypt::decrypt($social_provider->client_id),
            sprintf('services.%s.client_secret', $provider_name)=> Crypt::decrypt($social_provider->client_secret),
            sprintf('services.%s.redirect', $provider_name)=> route('auth_callback', ['provider_name' => $provider_name])
        ]);
    }

    function handle_redirect(string $provider_name) {
        $this->set_credentials($provider_name);

        return response()->json(['redirect_url' => Socialite::driver($provider_name)->stateless()->redirect()->getTargetUrl()]);
    }

    function handle_callback(string $provider_name) {
        $this->set_credentials($provider_name);
        $social_provider = SocialProvider::where('name', $provider_name)->firstOrFail();

        $provider_user = Socialite::driver('github')->stateless()->user();

        $linked_user = $social_provider->users()
            ->wherePivot('social_id', $provider_user->getId())
            ->first();

        $user_match_email = User::where('email', $provider_user->getEmail())->first();

        // No linked user and no user with email matches
        if (!$linked_user && !$user_match_email) {
            $user = User::create([
                    'name' => $provider_user->getName(),
                    'email' => $provider_user->getEmail(),
                ]);

            $social_provider->users()->attach($user, [
                'social_id' => $provider_user->getId(),
                'token' => $provider_user->token,
                'refresh_token' => $provider_user->refreshToken,
            ]);

            return response()->json($user);
        } else if (!$linked_user) {
            // No linked user but email matches
            return response()->json('There is existing account with this email. Please login to link this account.');
        }
        
        return response()->json($linked_user);
    }
}

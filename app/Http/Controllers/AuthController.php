<?php

namespace App\Http\Controllers;

use App\Models\SocialProvider;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    private function set_credentials(SocialProvider $social_provider)
    {
        config([
            sprintf('services.%s.client_id', $social_provider->name) => Crypt::decrypt($social_provider->client_id),
            sprintf('services.%s.client_secret', $social_provider->name)=> Crypt::decrypt($social_provider->client_secret),
            sprintf('services.%s.redirect', $social_provider->name)=> route('auth.callback', ['social_provider' => $social_provider->name])
        ]);
    }

    function handle_redirect(SocialProvider $social_provider) {
        $this->set_credentials($social_provider);

        return response()->json(['redirect_url' => Socialite::driver($social_provider->name)->stateless()->redirect()->getTargetUrl()]);
    }

    function handle_callback(SocialProvider $social_provider) {
        $this->set_credentials($social_provider);

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

            $linked_user = $user;
        } else if (!$linked_user) {
            // No linked user but email matches
            return response()->json([
                'success' => 'false',
                'message' => 'There is existing account with this email. Please login to link this account.'
            ], 401);
        }

        return response()->json($linked_user);
    }
}

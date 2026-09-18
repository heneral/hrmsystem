<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function redirect(string $provider)
    {
        abort_unless(in_array($provider, ['google', 'facebook'], true), 404);
        abort_if(! config("services.{$provider}.client_id"), 503, ucfirst($provider).' login is not configured.');

        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider)
    {
        abort_unless(in_array($provider, ['google', 'facebook'], true), 404);
        $socialUser = Socialite::driver($provider)->user();
        $user = User::firstOrNew(['oauth_provider' => $provider, 'oauth_provider_id' => $socialUser->getId()]);
        $user->name = $user->exists ? $user->name : ($socialUser->getName() ?: $socialUser->getNickname() ?: 'HotelHub guest');
        $user->email = $user->email ?: ($socialUser->getEmail() ?: $provider.'-'.Str::lower($socialUser->getId()).'@hotelhub.test');
        $user->password = $user->password ?: Str::random(40);
        $user->email_verified_at = $user->email_verified_at ?: now();
        $user->avatar_url = $socialUser->getAvatar();
        $user->save();
        $user->roles()->syncWithoutDetaching([Role::where('slug', 'customer')->value('id')]);
        Auth::login($user, true);

        return redirect()->intended(route('dashboard'));
    }
}
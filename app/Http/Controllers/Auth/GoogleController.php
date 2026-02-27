<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Exception;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $user = Socialite::driver('google')->stateless()->user();
            $existingUser = User::where('email', $user->email)->first();

            if ($existingUser) {
                $existingUser->update([
                    'google_id' => $user->id,
                    'email_verified_at' => $existingUser->email_verified_at ?? now(),
                ]);
                Auth::login($existingUser);
            } else {
                $newUser = User::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'google_id' => $user->id,
                    'role' => 'user',
                    'email_verified_at' => now(),
                    'password' => bcrypt(Str::random(16)),
                    'register_ip' => request()->ip(),
                    'last_login_ip' => request()->ip(),
                    'last_user_agent' => request()->header('User-Agent'),
                ]);

                $newUser->sendWelcomeNotification();
                Auth::login($newUser);
            }

            return redirect()->intended('dashboard');
        } catch (Exception $e) {
            return redirect('login')->with('error', 'Error during Google login.');
        }
    }
}

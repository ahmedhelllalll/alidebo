<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Models\BusinessProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Exception;

class FacebookController extends Controller
{
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->scopes(['email', 'public_profile'])->redirect();
    }

    public function handleFacebookCallback()
    {
        try {
            $socialUser = Socialite::driver('facebook')->user();
            $email = $socialUser->email ?? $socialUser->id . '@facebook.com';
            
            $isFallbackEmail = str_ends_with($email, '@facebook.com');
            
            $user = User::where('email', $email)->first();

            if (!$user) {
                $user = User::create([
                    'name' => $socialUser->name ?? 'Facebook User',
                    'email' => $email,
                    'facebook_id' => $socialUser->id,
                    'password' => Hash::make(Str::random(16)),
                    'role' => 'user',
                    'email_verified_at' => $isFallbackEmail ? null : now(),
                    'register_ip' => request()->ip(),
                    'last_login_ip' => request()->ip(),
                    'last_user_agent' => request()->header('User-Agent'),
                ]);
                $user->sendWelcomeNotification();
            } else {
                if (!$user->facebook_id) {
                    return redirect('login')->with('error', 'An account with this email already exists. Please log in with your password to link your account.');
                }
                $user->update([
                    'facebook_id' => $socialUser->id,
                    'last_login_ip' => request()->ip(),
                    'last_user_agent' => request()->header('User-Agent'),
                ]);
            }

            if (session()->has('claim_profile_id')) {
                $profile = BusinessProfile::find(session('claim_profile_id'));
                if ($profile && !$profile->is_claimed) {
                    $profile->update([
                        'user_id' => $user->id, 
                        'is_claimed' => true,
                        'status' => 'pending'
                    ]);
                }
                session()->forget('claim_profile_id');
            }

            Auth::login($user);
            session()->regenerate();

            return redirect()->intended('dashboard');
        } catch (Exception $e) {
            return redirect('login')->with('error', 'Error during Facebook login.');
        }
    }
}
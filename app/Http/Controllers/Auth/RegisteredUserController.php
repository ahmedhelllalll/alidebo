<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\BusinessProfile;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'register_ip' => $request->ip(),
            'last_login_ip' => $request->ip(),
            'last_user_agent' => $request->header('User-Agent'),
        ]);

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

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
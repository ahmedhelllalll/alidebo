<?php

namespace App\Http\Controllers;

use App\Models\BusinessProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClaimController extends Controller
{
    public function show(Request $request, $locale, $token = null)
    {
        if (!$token) {
            $token = $locale;
            $locale = app()->getLocale();
        }

        $business = BusinessProfile::where('claim_token', $token)->firstOrFail();

        if ($request->query('auto_claim') && Auth::check()) {
            return $this->process($request, $locale, $token);
        }

        // Redirect to the public business profile view with the claim token attached
        return redirect()->route('business.view', [
            'locale' => $locale,
            'slug' => $business->slug,
            'claim_token' => $token
        ]);
    }

    public function process(Request $request, $locale, $token = null)
    {
        if (!$token) {
            $token = $locale;
            $locale = app()->getLocale();
        }

        $business = BusinessProfile::where('claim_token', $token)->firstOrFail();

        if (!Auth::check()) {
            session(['url.intended' => route('business.claim.show', ['locale' => $locale, 'token' => $token, 'auto_claim' => 1])]);
            return redirect()->route('login')->with('info', __('auth.login_required_to_claim') ?? 'Login or register to claim this business.');
        }

        if (Auth::user()->businessProfile()->exists()) {
            return redirect()->route('business.view', ['locale' => $locale, 'slug' => $business->slug, 'claim_token' => $token])
                ->with('error', __('directory.already_own_business') ?? 'You already own a business profile and cannot claim another one.');
        }

        // Update ownership
        $business->update([
            'owner_id' => Auth::id(),
            'is_claimed' => true,
            'claim_token' => null // invalidate token
        ]);

        return redirect()->route('dashboard')->with('success', __('admin.welcome_claimed_successfully') ?? 'Welcome to your dashboard! You have successfully claimed your business profile.');
    }
}

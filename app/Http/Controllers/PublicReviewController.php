<?php

namespace App\Http\Controllers;

use App\Models\BusinessProfile;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PublicReviewController extends Controller
{
    public function store(Request $request, $slug)
    {
        $business = BusinessProfile::where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'reviewer_name' => 'required|string|max:255',
            'reviewer_email' => 'nullable|email|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ]);

        $ipAddress = $request->ip();

        // Anti-Spam: Check if this IP already reviewed this business
        $existingReview = Review::where('business_profile_id', $business->id)
                                ->where('ip_address', $ipAddress)
                                ->first();

        if ($existingReview) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => __('directory.already_reviewed')]);
            }
            return back()->with('error', __('directory.already_reviewed'));
        }

        Review::create([
            'business_profile_id' => $business->id,
            'reviewer_name' => $validated['reviewer_name'],
            'reviewer_email' => $validated['reviewer_email'] ?? null,
            'ip_address' => $ipAddress,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
            'status' => 'pending', // Pending by default
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => __('directory.review_submitted')]);
        }
        return back()->with('success', __('directory.review_submitted'));
    }
}

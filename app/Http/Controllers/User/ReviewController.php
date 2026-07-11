<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function index()
    {
        $business = Auth::user()->businessProfile;
        
        if (!$business) {
            return redirect()->route('dashboard')->with('error', 'You need a business profile first.');
        }

        $reviews = $business->reviews()->orderBy('created_at', 'desc')->paginate(10);

        return view('users.reviews.index', compact('reviews'));
    }

    public function reply(Request $request, Review $review)
    {
        $business = Auth::user()->businessProfile;
        
        if (!$business || $review->business_profile_id !== $business->id) {
            abort(403);
        }

        $request->validate([
            'reply' => 'required|string|max:1000',
        ]);

        $review->update([
            'reply' => $request->reply,
            'replied_at' => now(),
        ]);

        return back()->with('success', __('dashboard.index.review_replied'));
    }
}

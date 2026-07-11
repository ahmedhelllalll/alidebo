<?php

namespace App\Http\Controllers;

use App\Models\BusinessProfile;
use Illuminate\Http\Request;

class PublicLeadController extends Controller
{
    public function store(Request $request, $slug)
    {
        $business = BusinessProfile::where('slug', $slug)->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'message' => 'required|string|max:2000',
        ]);

        $lead = $business->leads()->create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'message' => $request->message,
            'status' => 'new',
        ]);

        if ($business->owner) {
            $business->owner->notify(new \App\Notifications\NewLeadNotification($lead));
        }

        return response()->json([
            'success' => true,
            'message' => __('directory.lead_success')
        ]);
    }
}

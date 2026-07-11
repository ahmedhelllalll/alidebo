<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $business = Auth::user()->businessProfile;
        
        if (!$business) {
            return redirect()->route('business.create');
        }

        $leads = $business->leads()->latest()->get();

        if ($request->ajax()) {
            return view('users.leads.partials.list', compact('leads'))->render();
        }

        return view('users.leads.index', compact('leads'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:new,contacted,converted,lost',
            'notes' => 'nullable|string|max:2000'
        ]);

        $business = Auth::user()->businessProfile;
        $lead = $business->leads()->findOrFail($id);

        $updateData = ['status' => $request->status];
        if ($request->has('notes')) {
            $updateData['notes'] = $request->notes;
        }

        $lead->update($updateData);

        return redirect()->back()->with('success', __('dashboard.index.lead_updated'));
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $query = ContactMessage::query();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && in_array($request->status, ['read', 'unread'])) {
            $query->where('status', $request->status);
        }

        $leads = $query->latest()->paginate(10)->withQueryString();

        if ($request->ajax()) {
            return view('admin.leads._list', compact('leads'))->render();
        }

        return view('admin.leads.index', compact('leads'));
    }

    public function show($id)
    {
        $lead = ContactMessage::findOrFail($id);
        
        // Mark as read if status is pending
        if ($lead->status === 'pending' || $lead->status === null || $lead->status === '') {
            $lead->update(['status' => 'read']);
        }

        return view('admin.leads.show', compact('lead'));
    }

    public function destroy($id)
    {
        $lead = ContactMessage::findOrFail($id);
        $lead->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('admin.success') ?? 'Deleted successfully.'
            ]);
        }

        return redirect()->route('admin.leads.index')->with('success', __('admin.success') ?? 'Deleted successfully.');
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:read,unread'
        ]);

        $lead = ContactMessage::findOrFail($id);
        $lead->update(['status' => $validated['status']]);

        return response()->json([
            'success' => true,
            'message' => __('admin.status_updated') ?? 'Status updated successfully.'
        ]);
    }
}

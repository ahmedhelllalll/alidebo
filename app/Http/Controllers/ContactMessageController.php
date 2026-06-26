<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'min:2', 'max:50'],
            'last_name'  => ['required', 'string', 'min:2', 'max:50'],
            'email'      => ['required', 'email', 'max:255'],
            'message'    => ['required', 'string', 'min:10', 'max:2000'],
        ], [
            'first_name.required' => __('landing.contact_error_fname_required') ?? 'First name is required.',
            'last_name.required'  => __('landing.contact_error_lname_required') ?? 'Last name is required.',
            'email.required'      => __('landing.contact_error_email_required') ?? 'Email is required.',
            'email.email'         => __('landing.contact_error_email_invalid') ?? 'Please enter a valid email address.',
            'message.required'    => __('landing.contact_error_msg_required') ?? 'A message is required.',
            'message.min'         => __('landing.contact_error_msg_min') ?? 'Message must be at least 10 characters.',
        ]);

        ContactMessage::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => __('landing.contact_success') ?? 'Thank you for reaching out. We will get back to you shortly.'
        ]);
    }
}

<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function fetchUnread()
    {
        $user = Auth::user();
        
        return response()->json([
            'count' => $user->unreadNotifications->count(),
            'notifications' => $user->unreadNotifications()->latest()->take(5)->get()
        ]);
    }

    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        
        return response()->json(['success' => true]);
    }
}

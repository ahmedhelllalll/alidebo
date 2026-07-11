<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class SupportChatController extends Controller
{
    public function index()
    {
        $sessions = ChatSession::with('user', 'messages')
            ->orderBy('updated_at', 'desc')
            ->paginate(15);

        return view('admin.support.index', compact('sessions'));
    }

    public function show(ChatSession $support_chat)
    {
        // Mark user messages as read
        $support_chat->messages()
            ->where('sender_type', 'user')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        // If AJAX request, return messages
        if (request()->ajax()) {
            return response()->json([
                'messages' => $support_chat->messages()->orderBy('created_at', 'asc')->get()->map(function ($msg) {
                    return [
                        'id' => $msg->id,
                        'message' => $msg->message,
                        'sender_type' => $msg->sender_type,
                        'time_ago' => $msg->created_at->format('h:i A'),
                    ];
                }),
                'is_user_typing' => Cache::has('chat_typing_' . $support_chat->id . '_user')
            ]);
        }

        return view('admin.support.show', compact('support_chat'));
    }

    public function sendMessage(Request $request, ChatSession $support_chat)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $message = $support_chat->messages()->create([
            'sender_type' => 'admin',
            'sender_id' => Auth::id(),
            'message' => $request->message,
        ]);

        $support_chat->touch(); // Update the session's updated_at timestamp

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $message->id,
                'message' => $message->message,
                'sender_type' => $message->sender_type,
                'time_ago' => $message->created_at->format('h:i A'),
            ]
        ]);
    }

    public function close(ChatSession $support_chat)
    {
        $support_chat->update(['status' => 'closed']);
        return redirect()->route('admin.support-chats.index')->with('success', 'Chat session closed successfully.');
    }

    public function typing(ChatSession $support_chat)
    {
        Cache::put('chat_typing_' . $support_chat->id . '_admin', true, now()->addSeconds(3));
        return response()->json(['success' => true]);
    }
}

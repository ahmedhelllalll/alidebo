<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ChatSession;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class SupportChatController extends Controller
{
    /**
     * Display the support chat interface.
     */
    public function index()
    {
        return view('users.support.chat');
    }

    /**
     * Fetch messages for the user's active chat session.
     */
    public function fetchMessages()
    {
        $session = ChatSession::firstOrCreate(
            ['user_id' => Auth::id(), 'status' => 'open']
        );

        // Mark unread messages from admin/bot as read
        $session->messages()
            ->where('sender_type', '!=', 'user')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = $session->messages()->orderBy('created_at', 'asc')->get();

        return response()->json([
            'session_id' => $session->id,
            'messages' => $messages->map(function ($msg) {
                return [
                    'id' => $msg->id,
                    'message' => $msg->message,
                    'sender_type' => $msg->sender_type,
                    'created_at' => $msg->created_at->format('Y-m-d H:i:s'),
                    'time_ago' => $msg->created_at->diffForHumans(),
                ];
            }),
            'is_admin_typing' => Cache::has('chat_typing_' . $session->id . '_admin')
        ]);
    }

    /**
     * Send a new message.
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $session = ChatSession::firstOrCreate(
            ['user_id' => Auth::id(), 'status' => 'open']
        );

        $isFirstMessage = $session->messages()->count() === 0;

        // Save user message
        $userMessage = $session->messages()->create([
            'sender_type' => 'user',
            'sender_id' => Auth::id(),
            'message' => $request->message,
        ]);

        $autoResponse = null;

        // Auto-reply logic for the first message
        if ($isFirstMessage) {
            $autoResponse = $session->messages()->create([
                'sender_type' => 'bot',
                'message' => __('dashboard.index.auto_reply_msg') ?? 'Thank you for reaching out! An elite support agent will be with you shortly.',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $userMessage->id,
                'message' => $userMessage->message,
                'sender_type' => $userMessage->sender_type,
                'time_ago' => $userMessage->created_at->diffForHumans(),
            ],
            'auto_response' => $autoResponse ? [
                'id' => $autoResponse->id,
                'message' => $autoResponse->message,
                'sender_type' => $autoResponse->sender_type,
                'time_ago' => $autoResponse->created_at->diffForHumans(),
            ] : null
        ]);
    }

    /**
     * Set the user typing indicator status.
     */
    public function typing(Request $request)
    {
        $session = ChatSession::where('user_id', Auth::id())->where('status', 'open')->first();
        if ($session) {
            Cache::put('chat_typing_' . $session->id . '_user', true, now()->addSeconds(3));
        }
        return response()->json(['success' => true]);
    }
}

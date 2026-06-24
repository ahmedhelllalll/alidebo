<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AiChatService;

class ChatbotController extends Controller
{
    private $chatService;

    public function __construct(AiChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    public function ask(Request $request)
    {
        $request->validate([
            'messages' => 'required|array',
            'messages.*.role' => 'required|in:user,assistant',
            'messages.*.content' => 'required|string|max:1000'
        ]);

        $conversationHistory = $request->input('messages');
        
        $response = $this->chatService->getResponse($conversationHistory);

        return response()->json([
            'success' => true,
            'reply' => $response
        ]);
    }
}

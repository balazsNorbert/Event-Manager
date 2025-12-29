<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class AgentController extends Controller
{
    public function pendingChats()
    {
        return User::whereHas('messages', function ($query) {
            $query->where('sender_type', 'bot')
                  ->where('message', 'like', '%I am transferring you to a human agent now.%')
                  ->where('is_handled', false);
        })
        ->with(['messages' => function($q) {
            $q->latest()->limit(20);
        }])
        ->get();
    }

    public function reply(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'message' => 'required|string'
        ]);

        return Message::create([
            'user_id' => $request->user_id,
            'message' => $request->message,
            'sender_type' => 'agent'
        ]);
    }

    public function closeChat($userId)
    {
        Message::where('user_id', $userId)
            ->where('message', 'like', '%I am transferring you to a human agent now.%')
            ->update(['is_handled' => true]);

        return response()->json(['message' => 'Chat closed successfully']);
    }
}

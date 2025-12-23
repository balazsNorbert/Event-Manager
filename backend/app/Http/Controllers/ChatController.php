<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Tymon\JWTAuth\Facades\JWTAuth;

class ChatController extends Controller
{
    public function sendMessage(Request $request)
    {
        $userText = $request->input('message');
        $user = JWTAuth::parseToken()->authenticate();
        Message::create([
            'user_id' => $user->id,
            'message' => $userText,
            'sender_type' => 'user'
        ]);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . env('GEMINI_API_KEY'), [
            'contents' => [
                ['parts' => [['text' => "You are a Helpdesk Assistant for an Event Manager App. Users can: Create events (title, date, description),
                list events, update descriptions, or delete events. If the user wants to talk to a human, answer: 'I am transferring you to a human
                agent now.' If they ask about password reset, tell them to use the 'Forgot Password' link on the login page. Your goal is to support the user.
                If they greet you, greet them back. If they ask a question, answer it briefly. If their input is unclear, ask for clarification politely.
                Always respond in their language. The user reply is:" . $userText]]]
            ]
        ]);

        $data = $response->json();
        $aiReply = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

        if (!$aiReply) {
            $aiReply = "I'm sorry, I'm having a bit of trouble understanding that. Could you please rephrase your question? I'm here to help!";
        }

        Message::create([
            'user_id' => $user->id,
            'message' => $aiReply,
            'sender_type' => 'bot'
        ]);

        return response()->json(['reply' => $aiReply]);
    }
}

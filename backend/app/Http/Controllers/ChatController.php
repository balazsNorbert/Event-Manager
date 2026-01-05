<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Tymon\JWTAuth\Facades\JWTAuth;

class ChatController extends Controller
{
    public function getMessages()
    {
        $user = JWTAuth::parseToken()->authenticate();

        $messages = Message::where('user_id', $user->id)
                    ->orderBy('created_at', 'asc')
                    ->get();

        return response()->json($messages);
    }

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
                ['parts' => [['text' => "You are a Helpdesk Assistant for an Event Manager Web App. Users can manage events on their Dashboard.
                To Create: Use the 'Create New Event' button and fill the form.
                To Edit/Delete: These options are found directly on each event card in the list. Look for the pencil icon to edit and the 'Delete event' button to delete an event.
                If they forgot password: Use the 'Forgot Password' link on the login page.
                Human Agent: If they want a human or an agent, you MUST say: 'I am transferring you to a human agent now. Your goal is to support the user
                only regarding the Event Manager Web App.
                If they greet you, greet them back. If they ask a question, answer it briefly. If their input is unclear, ask for clarification politely.
                Always respond in their language. The user reply is:" . $userText]]]
            ]
        ]);

        $data = $response->json();
        $aiReply = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

        if (!$aiReply) {
            $aiReply = "I'm sorry, I'm having a bit of trouble understanding that. Could you please rephrase your question? I'm here to help!";
        }

        $isTransferring = str_contains($aiReply, 'I am transferring you to a human agent now.');

        Message::create([
            'user_id' => $user->id,
            'message' => $aiReply,
            'sender_type' => 'bot'
        ]);

        return response()->json([
          'reply' => $aiReply,
          'is_waiting_for_agent' => $isTransferring
        ]);
    }

    public function clearChat()
    {
        $user = JWTAuth::parseToken()->authenticate();

        if (!$user->id) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        Message::where('user_id', $user->id)->delete();

        return response()->json(['message' => 'Chat history deleted successfully']);
    }
}

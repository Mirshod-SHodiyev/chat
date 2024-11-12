<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    // Xabar yuborish
    public function sendMessage(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'receiver_id' => 'required|exists:users,id', // Check if receiver exists in the users table
            'content' => 'required|string|max:255', // The message content
        ]);

        // Create a new message record
        $message = Message::create([
            'sender_id' => Auth::id(), // Get the ID of the authenticated user
            'receiver_id' => $request->receiver_id, // Receiver's user ID
            'content' => $request->content, // Message content
        ]);

        // Return the created message as a JSON response
        return response()->json($message);
    }

    // Foydalanuvchiga tegishli xabarlarni olish
    public function getMessages($userId)
    {
        // Retrieve messages between the authenticated user and the given user
        $messages = Message::where(function ($query) use ($userId) {
            // Messages sent by the authenticated user to the given user
            $query->where('sender_id', Auth::id())
                  ->where('receiver_id', $userId);
        })->orWhere(function ($query) use ($userId) {
            // Messages sent by the given user to the authenticated user
            $query->where('sender_id', $userId)
                  ->where('receiver_id', Auth::id());
        })->get();

        // Return messages as a JSON response
        return response()->json($messages);
    }
}

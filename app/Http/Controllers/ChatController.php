<?php

namespace App\Http\Controllers;

use App\Events\GotMessage;
use App\Jobs\SendMessage;
use App\Models\Message;
use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{

    
    public function index(): Application|Factory|View
    {
        $users = User::where('id', '!=', auth()->id())->get();
    
        $messages = [];
    
        foreach ($users as $user) {
            $latestMessage = Message::where(function ($query) use ($user) {
                $query->where('sender_id', auth()->id())
                      ->where('receiver_id', $user->id);
            })->orWhere(function ($query) use ($user) {
                $query->where('sender_id', $user->id)
                      ->where('receiver_id', auth()->id());
            })->latest()->take(1)->first();
    
            $messages[$user->id] = $latestMessage;
        }
    
        return view('chat', compact('users', 'messages'));
    }
    

    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $id)
    {
        $validated = $request->validate([
            'message' => ['required', 'string'],
        ]);
    
        $message = Message::create([
            'message' => $validated['message'],
            'sender_id' => Auth::id(),
            'receiver_id' => $id,
        ]);
    
        // Optionally, broadcast the message or dispatch events
        GotMessage::dispatch($message);
    
        return back()->with('success', 'Message sent.');
    }
    

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Fetch the user who the chat is with
        $user = User::find($id);
    
        // If the user is not found, return a 404 error
        if (!$user) {
            abort(404); // Handle user not found
        }
    
        // Get the current logged-in user's ID
        $auth = Auth::id();
    
        // Get the partner user's ID (the one you're chatting with)
        $partner = $user->id;
    
        // Fetch the messages between the logged-in user and the selected user
        $messages = Message::query()
            ->where(function ($query) use ($auth, $partner) {
                $query->where('sender_id', $auth)
                    ->where('receiver_id', $partner);
            })
            ->orWhere(function ($query) use ($auth, $partner) {
                $query->where('sender_id', $partner)
                    ->where('receiver_id', $auth);
            })
            ->get();
    
        // Fetch all users except the logged-in user
        $users = User::where('id', '!=', $auth)->get();
    
        // Return the view with the user, messages, and the users list
        return view('chat', ['user' => $user, 'messages' => $messages, 'users' => $users]);
    }
    
    

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function getMessages($id): JsonResponse
    {
        $user = User::query()->find($id);

        $auth    = Auth::id();
        $partner = $user->id;

        $messages = Message::query()
            ->where(function ($query) use ($auth, $partner) {
                $query->where('sender_id', $auth)
                    ->where('receiver_id', $partner);
            })->orWhere(function ($query) use ($auth, $partner) {
                $query->where('sender_id', $partner)
                    ->where('receiver_id', $auth);
            })->with(['sender', 'receiver'])->get();

        return response()->json($messages);
    }

    public function storeMessages(): JsonResponse
    {
        $message = Message::query()->create([
            'sender_id' => request('sender_id'),
            'receiver_id' => request('receiver_id'),
            'message' => request('message'),
        ]);

        SendMessage::dispatch($message);

        return response()->json($message);
    }
}

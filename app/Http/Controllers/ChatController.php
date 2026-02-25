<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        $users = User::where('id', '!=', Auth::id())->get()->map(function($user) {
            $user->unread_count = Message::where('sender_id', $user->id)
                ->where('receiver_id', Auth::id())
                ->where('is_read', false)
                ->count();
            return $user;
        });
        return response()->json($users);
    }

    public function fetchMessages($receiverId)
    {
        $messages = Message::where(function($q) use ($receiverId) {
            $q->where('sender_id', Auth::id())->where('receiver_id', $receiverId);
        })->orWhere(function($q) use ($receiverId) {
            $q->where('sender_id', $receiverId)->where('receiver_id', Auth::id());
        })->orderBy('created_at', 'asc')->get();

        // Mark as read
        Message::where('sender_id', $receiverId)
            ->where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return response()->json($messages);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);

        return response()->json($message);
    }

    public function updateStatus()
    {
        if (Auth::check()) {
            Auth::user()->update(['last_seen_at' => now()]);
        }
        
        $unreadCount = Message::where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return response()->json(['status' => 'success', 'unread_count' => $unreadCount]);
    }
}

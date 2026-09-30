<?php
namespace App\Http\Controllers\Api\V1\User;
use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index(Request $request) {
        $messages = ChatMessage::where('sender_id', $request->user()->id)
            ->orWhere('receiver_id', $request->user()->id)
            ->with(['sender', 'receiver'])
            ->get();
        return response()->json(['status' => 'success', 'data' => $messages]);
    }
    public function store(Request $request) {
        $validated = $request->validate([
            'message' => 'required|string',
        ]);
        $admin = User::where('role', 'admin')->first();
        $chat = ChatMessage::create([
            'sender_id' => $request->user()->id,
            'receiver_id' => $admin ? $admin->id : null,
            'message' => $validated['message']
        ]);
        return response()->json(['status' => 'success', 'data' => $chat]);
    }
}
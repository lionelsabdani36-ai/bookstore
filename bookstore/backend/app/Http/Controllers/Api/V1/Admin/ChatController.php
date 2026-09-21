<?php
namespace App\Http\Controllers\Api\V1\Admin;
use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index(Request $request) {
        $messages = ChatMessage::with(['sender', 'receiver'])->get();
        return response()->json(['status' => 'success', 'data' => $messages]);
    }
    public function store(Request $request) {
        $validated = $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);
        $validated['sender_id'] = $request->user()->id;
        $chat = ChatMessage::create($validated);
        return response()->json(['status' => 'success', 'data' => $chat]);
    }
}
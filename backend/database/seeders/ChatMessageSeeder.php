<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\ChatMessage;
class ChatMessageSeeder extends Seeder
{
    public function run(): void
    {
        ChatMessage::create([
            'sender_id' => 2,
            'receiver_id' => 1,
            'message' => 'Hello admin, I need help.'
        ]);
    }
}
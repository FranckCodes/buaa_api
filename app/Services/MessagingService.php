<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\Message;
use Illuminate\Support\Facades\DB;

class MessagingService
{
    public function startConversation(array $participantIds): Conversation
    {
        return DB::transaction(function () use ($participantIds) {
            $conversation = Conversation::create();

            foreach (array_unique($participantIds) as $userId) {
                ConversationParticipant::create([
                    'conversation_id' => $conversation->id,
                    'user_id'         => $userId,
                    'unread_count'    => 0,
                ]);
            }

            return $conversation->load('participants');
        });
    }

    public function sendMessage(Conversation $conversation, int $senderId, array $payload): Message
    {
        return DB::transaction(function () use ($conversation, $senderId, $payload) {
            $message = Message::create([
                'conversation_id'      => $conversation->id,
                'sender_id'            => $senderId,
                'text'                 => $payload['text'] ?? null,
                'type'                 => $payload['type'] ?? 'text',
                'image_url'            => $payload['image_url'] ?? null,
                'file_url'             => $payload['file_url'] ?? null,
                'reply_to_message_id'  => $payload['reply_to_message_id'] ?? null,
                'status'               => 'sent',
            ]);

            $conversation->update(['last_message_at' => now()]);

            ConversationParticipant::where('conversation_id', $conversation->id)
                ->where('user_id', '!=', $senderId)
                ->increment('unread_count');

            return $message->load('sender');
        });
    }

    public function markAsRead(Conversation $conversation, int $userId): void
    {
        ConversationParticipant::where('conversation_id', $conversation->id)
            ->where('user_id', $userId)
            ->update(['unread_count' => 0, 'last_read_at' => now()]);
    }

    public function getUserInbox(int $userId)
    {
        return Conversation::whereHas('participants', fn ($q) => $q->where('user_id', $userId))
            ->with(['participants', 'messages' => fn ($q) => $q->latest()->limit(20)])
            ->latest('last_message_at')
            ->get();
    }
}

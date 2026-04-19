<?php

namespace App\Services;

use App\Exceptions\BusinessException;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\Message;
use Illuminate\Support\Facades\DB;

class MessagingService
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    public function startConversation(array $participantIds): Conversation
    {
        $unique = collect($participantIds)->unique()->values()->all();

        if (count($unique) < 2) {
            throw new BusinessException('Une conversation doit contenir au moins deux participants.', 422);
        }

        return DB::transaction(function () use ($unique) {
            $conversation = Conversation::create();

            foreach ($unique as $userId) {
                ConversationParticipant::create([
                    'conversation_id' => $conversation->id,
                    'user_id'         => $userId,
                    'unread_count'    => 0,
                ]);
            }

            return $conversation->load('participants', 'participantRows');
        });
    }

    public function sendMessage(Conversation $conversation, string $senderId, array $payload): Message
    {
        if (!$conversation->participants()->where('users.id', $senderId)->exists()) {
            throw new BusinessException("L'utilisateur n'est pas participant à cette conversation.", 403);
        }

        return DB::transaction(function () use ($conversation, $senderId, $payload) {
            $message = Message::create([
                'conversation_id'     => $conversation->id,
                'sender_id'           => $senderId,
                'text'                => $payload['text'] ?? null,
                'type'                => $payload['type'] ?? 'text',
                'image_url'           => $payload['image_url'] ?? null,
                'file_url'            => $payload['file_url'] ?? null,
                'reply_to_message_id' => $payload['reply_to_message_id'] ?? null,
                'status'              => 'sent',
            ]);

            $conversation->update(['last_message_at' => now()]);

            ConversationParticipant::where('conversation_id', $conversation->id)
                ->where('user_id', '!=', $senderId)
                ->increment('unread_count');

            $recipients = ConversationParticipant::where('conversation_id', $conversation->id)
                ->where('user_id', '!=', $senderId)
                ->pluck('user_id');

            foreach ($recipients as $recipientId) {
                $this->notificationService->create([
                    'user_id'      => $recipientId,
                    'category'     => 'app',
                    'type'         => 'info',
                    'title'        => 'Nouveau message',
                    'body'         => 'Vous avez reçu un nouveau message.',
                    'from_user_id' => $senderId,
                ]);
            }

            return $message->load('sender', 'replyTo');
        });
    }

    public function markAsRead(Conversation $conversation, string $userId): void
    {
        $updated = ConversationParticipant::where('conversation_id', $conversation->id)
            ->where('user_id', $userId)
            ->update(['unread_count' => 0, 'last_read_at' => now(), 'updated_at' => now()]);

        if (!$updated) {
            throw new BusinessException('Utilisateur non trouvé dans cette conversation.', 403);
        }

        Message::where('conversation_id', $conversation->id)
            ->where('sender_id', '!=', $userId)
            ->where('status', '!=', 'read')
            ->update(['status' => 'read', 'updated_at' => now()]);
    }

    public function getUserInbox(string $userId)
    {
        return Conversation::whereHas('participants', fn ($q) => $q->where('users.id', $userId))
            ->with([
                'participants',
                'participantRows',
                'messages' => fn ($q) => $q->with('sender', 'replyTo')->latest()->limit(20),
            ])
            ->latest('last_message_at')
            ->paginate(15);
    }
}

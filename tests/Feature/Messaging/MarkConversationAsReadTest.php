<?php

namespace Tests\Feature\Messaging;

use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithRoles;
use Tests\TestCase;

class MarkConversationAsReadTest extends TestCase
{
    use RefreshDatabase, InteractsWithRoles;

    public function test_user_can_mark_conversation_as_read(): void
    {
        $this->seed();

        $userA = $this->createUserWithRole('client');
        $userB = $this->createUserWithRole('client');

        $conversation = Conversation::create();
        ConversationParticipant::create(['conversation_id' => $conversation->id, 'user_id' => $userA->id, 'unread_count' => 2]);
        ConversationParticipant::create(['conversation_id' => $conversation->id, 'user_id' => $userB->id, 'unread_count' => 0]);

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => $userB->id,
            'text'            => 'Salut',
            'type'            => 'text',
            'status'          => 'sent',
        ]);

        $this->actingAs($userA, 'sanctum')
            ->postJson("/api/conversations/{$conversation->id}/read", ['user_id' => $userA->id])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('conversation_participants', [
            'conversation_id' => $conversation->id,
            'user_id'         => $userA->id,
            'unread_count'    => 0,
        ]);
    }
}

<?php

namespace Tests\Feature\Messaging;

use App\Models\Conversation;
use App\Models\ConversationParticipant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithRoles;
use Tests\TestCase;

class SendMessageTest extends TestCase
{
    use RefreshDatabase, InteractsWithRoles;

    public function test_participant_can_send_message(): void
    {
        $this->seed();

        $userA = $this->createUserWithRole('client');
        $userB = $this->createUserWithRole('client');

        $conversation = Conversation::create();
        ConversationParticipant::create(['conversation_id' => $conversation->id, 'user_id' => $userA->id]);
        ConversationParticipant::create(['conversation_id' => $conversation->id, 'user_id' => $userB->id]);

        $this->actingAs($userA, 'sanctum')
            ->postJson("/api/conversations/{$conversation->id}/messages", [
                'sender_id' => $userA->id,
                'text'      => 'Bonjour',
                'type'      => 'text',
            ])
            ->assertCreated()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('messages', [
            'conversation_id' => $conversation->id,
            'sender_id'       => $userA->id,
            'text'            => 'Bonjour',
        ]);
    }

    public function test_non_participant_cannot_send_message(): void
    {
        $this->seed();

        $userA    = $this->createUserWithRole('client');
        $userB    = $this->createUserWithRole('client');
        $outsider = $this->createUserWithRole('client');

        $conversation = Conversation::create();
        ConversationParticipant::create(['conversation_id' => $conversation->id, 'user_id' => $userA->id]);
        ConversationParticipant::create(['conversation_id' => $conversation->id, 'user_id' => $userB->id]);

        $this->actingAs($outsider, 'sanctum')
            ->postJson("/api/conversations/{$conversation->id}/messages", [
                'sender_id' => $outsider->id,
                'text'      => 'Intrusion',
                'type'      => 'text',
            ])
            ->assertForbidden();
    }
}

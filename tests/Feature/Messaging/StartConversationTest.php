<?php

namespace Tests\Feature\Messaging;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithRoles;
use Tests\TestCase;

class StartConversationTest extends TestCase
{
    use RefreshDatabase, InteractsWithRoles;

    public function test_user_can_start_conversation(): void
    {
        $this->seed();

        $userA = $this->createUserWithRole('client');
        $userB = $this->createUserWithRole('client');

        $this->actingAs($userA, 'sanctum')
            ->postJson('/api/conversations', ['participant_ids' => [$userA->id, $userB->id]])
            ->assertCreated()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('conversation_participants', ['user_id' => $userA->id]);
        $this->assertDatabaseHas('conversation_participants', ['user_id' => $userB->id]);
    }

    public function test_cannot_start_conversation_with_single_participant(): void
    {
        $this->seed();

        $userA = $this->createUserWithRole('client');

        $this->actingAs($userA, 'sanctum')
            ->postJson('/api/conversations', ['participant_ids' => [$userA->id, $userA->id]])
            ->assertStatus(422)
            ->assertJsonPath('success', false);
    }
}

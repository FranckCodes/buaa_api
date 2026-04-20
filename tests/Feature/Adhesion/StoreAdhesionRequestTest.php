<?php

namespace Tests\Feature\Adhesion;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithRoles;
use Tests\TestCase;

class StoreAdhesionRequestTest extends TestCase
{
    use RefreshDatabase, InteractsWithRoles;

    public function test_client_can_create_adhesion_request(): void
    {
        $this->seed();

        $user = $this->createUserWithRole('client');

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/adhesion-requests', [
                'nom'            => 'Coopérative Test',
                'demandeur_type' => 'organisation',
                'telephone'      => '+243900000000',
            ])
            ->assertCreated()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('adhesion_requests', [
            'nom'            => 'Coopérative Test',
            'demandeur_type' => 'organisation',
        ]);
    }

    public function test_adhesion_request_requires_required_fields(): void
    {
        $this->seed();

        $user = $this->createUserWithRole('client');

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/adhesion-requests', [])
            ->assertStatus(422)
            ->assertJsonPath('success', false);
    }
}

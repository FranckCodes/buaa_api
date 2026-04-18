<?php

namespace Tests\Feature\Credit;

use App\Models\Client;
use App\Models\Reference\CreditType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithRoles;
use Tests\TestCase;

class StoreCreditTest extends TestCase
{
    use RefreshDatabase, InteractsWithRoles;

    public function test_client_can_create_credit_request(): void
    {
        $this->seed();

        $user   = $this->createUserWithRole('client');
        $client = Client::factory()->create(['user_id' => $user->id]);
        $type   = CreditType::firstOrFail();

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/credits', [
                'client_id'       => $client->id,
                'credit_type_id'  => $type->id,
                'montant_demande' => 500,
                'duree_mois'      => 6,
                'objet_credit'    => 'Achat matériel',
            ])
            ->assertCreated()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('credits', [
            'client_id'       => $client->id,
            'montant_demande' => 500,
        ]);
    }

    public function test_credit_creation_requires_required_fields(): void
    {
        $this->seed();

        $user = $this->createUserWithRole('client');

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/credits', [])
            ->assertStatus(422)
            ->assertJsonStructure(['success', 'message', 'data', 'meta', 'errors'])
            ->assertJsonPath('success', false);
    }
}

<?php

namespace Tests\Feature\Insurance;

use App\Models\Client;
use App\Models\Reference\InsuranceType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithRoles;
use Tests\TestCase;

class StoreInsuranceTest extends TestCase
{
    use RefreshDatabase, InteractsWithRoles;

    public function test_client_can_create_insurance_subscription(): void
    {
        $this->seed();

        $user   = $this->createUserWithRole('client');
        $client = Client::factory()->create(['id' => $user->id]);
        $type   = InsuranceType::firstOrFail();

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/insurances', [
                'client_id'         => $client->id,
                'insurance_type_id' => $type->id,
                'montant_annuel'    => 150,
                'description'       => 'Souscription assurance test',
            ])
            ->assertCreated()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('insurances', [
            'client_id'      => $client->id,
            'montant_annuel' => 150,
        ]);
    }

    public function test_insurance_creation_requires_required_fields(): void
    {
        $this->seed();

        $user = $this->createUserWithRole('client');

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/insurances', [])
            ->assertStatus(422)
            ->assertJsonPath('success', false);
    }
}

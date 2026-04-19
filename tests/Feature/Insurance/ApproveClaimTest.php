<?php

namespace Tests\Feature\Insurance;

use App\Models\Client;
use App\Models\Insurance;
use App\Models\InsuranceClaim;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithRoles;
use Tests\TestCase;

class ApproveClaimTest extends TestCase
{
    use RefreshDatabase, InteractsWithRoles;

    public function test_admin_can_approve_insurance_claim(): void
    {
        $this->seed();

        $admin      = $this->createUserWithRole('admin');
        $clientUser = $this->createUserWithRole('client');
        $client     = Client::factory()->create(['id' => $clientUser->id]);
        $insurance  = Insurance::factory()->create(['client_id' => $client->id]);
        $claim      = InsuranceClaim::factory()->create(['insurance_id' => $insurance->id, 'client_id' => $client->id]);

        $this->actingAs($admin, 'sanctum')
            ->postJson("/api/insurance-claims/{$claim->id}/approve", [
                'amount'       => 250,
                'processed_by' => $admin->id,
            ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('insurance_claims', [
            'id'               => $claim->id,
            'montant_approuve' => 250,
            'statut'           => 'approuve',
        ]);
    }

    public function test_admin_can_reject_insurance_claim(): void
    {
        $this->seed();

        $admin      = $this->createUserWithRole('admin');
        $clientUser = $this->createUserWithRole('client');
        $client     = Client::factory()->create(['id' => $clientUser->id]);
        $insurance  = Insurance::factory()->create(['client_id' => $client->id]);
        $claim      = InsuranceClaim::factory()->create(['insurance_id' => $insurance->id, 'client_id' => $client->id]);

        $this->actingAs($admin, 'sanctum')
            ->postJson("/api/insurance-claims/{$claim->id}/reject", ['processed_by' => $admin->id])
            ->assertOk();

        $this->assertEquals('rejete', $claim->fresh()->statut);
    }
}

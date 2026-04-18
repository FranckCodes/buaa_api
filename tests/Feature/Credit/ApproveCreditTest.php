<?php

namespace Tests\Feature\Credit;

use App\Models\Client;
use App\Models\Credit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithRoles;
use Tests\TestCase;

class ApproveCreditTest extends TestCase
{
    use RefreshDatabase, InteractsWithRoles;

    public function test_admin_can_approve_credit(): void
    {
        $this->seed();

        $admin      = $this->createUserWithRole('admin');
        $clientUser = $this->createUserWithRole('client');
        $client     = Client::factory()->create(['user_id' => $clientUser->id]);
        $credit     = Credit::factory()->create(['client_id' => $client->id]);

        $this->actingAs($admin, 'sanctum')
            ->postJson("/api/credits/{$credit->id}/approve", [
                'montant_approuve' => 400,
                'montant_echeance' => 100,
                'traite_par'       => $admin->id,
            ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertEquals(400, (float) $credit->fresh()->montant_approuve);
        $this->assertDatabaseHas('credit_payments', ['credit_id' => $credit->id]);
    }

    public function test_client_cannot_approve_credit(): void
    {
        $this->seed();

        $clientUser = $this->createUserWithRole('client');
        $client     = Client::factory()->create(['user_id' => $clientUser->id]);
        $credit     = Credit::factory()->create(['client_id' => $client->id]);

        $this->actingAs($clientUser, 'sanctum')
            ->postJson("/api/credits/{$credit->id}/approve", [
                'montant_approuve' => 400,
                'traite_par'       => $clientUser->id,
            ])
            ->assertForbidden();
    }
}

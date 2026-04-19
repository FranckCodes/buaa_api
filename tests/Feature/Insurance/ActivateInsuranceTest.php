<?php

namespace Tests\Feature\Insurance;

use App\Models\Client;
use App\Models\Insurance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithRoles;
use Tests\TestCase;

class ActivateInsuranceTest extends TestCase
{
    use RefreshDatabase, InteractsWithRoles;

    public function test_admin_can_activate_insurance(): void
    {
        $this->seed();

        $admin      = $this->createUserWithRole('admin');
        $clientUser = $this->createUserWithRole('client');
        $client     = Client::factory()->create(['id' => $clientUser->id]);
        $insurance  = Insurance::factory()->create(['client_id' => $client->id]);

        $this->actingAs($admin, 'sanctum')
            ->postJson("/api/insurances/{$insurance->id}/activate", ['processed_by' => $admin->id])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertEquals('active', $insurance->fresh()->status->code);
    }

    public function test_client_cannot_activate_insurance(): void
    {
        $this->seed();

        $clientUser = $this->createUserWithRole('client');
        $client     = Client::factory()->create(['id' => $clientUser->id]);
        $insurance  = Insurance::factory()->create(['client_id' => $client->id]);

        $this->actingAs($clientUser, 'sanctum')
            ->postJson("/api/insurances/{$insurance->id}/activate", ['processed_by' => $clientUser->id])
            ->assertForbidden();
    }

    public function test_cannot_activate_already_active_insurance(): void
    {
        $this->seed();

        $admin      = $this->createUserWithRole('admin');
        $clientUser = $this->createUserWithRole('client');
        $client     = Client::factory()->create(['id' => $clientUser->id]);
        $insurance  = Insurance::factory()->create(['client_id' => $client->id]);

        // Première activation
        $this->actingAs($admin, 'sanctum')
            ->postJson("/api/insurances/{$insurance->id}/activate", ['processed_by' => $admin->id]);

        // Deuxième activation — doit échouer
        $this->actingAs($admin, 'sanctum')
            ->postJson("/api/insurances/{$insurance->id}/activate", ['processed_by' => $admin->id])
            ->assertStatus(422)
            ->assertJsonPath('success', false);
    }
}

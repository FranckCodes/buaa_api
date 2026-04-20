<?php

namespace Tests\Feature\Support;

use App\Models\Client;
use App\Models\SupportTicket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithRoles;
use Tests\TestCase;

class AssignSupportTicketTest extends TestCase
{
    use RefreshDatabase, InteractsWithRoles;

    public function test_admin_can_assign_support_ticket(): void
    {
        $this->seed();

        $admin      = $this->createUserWithRole('admin');
        $clientUser = $this->createUserWithRole('client');
        $client     = Client::factory()->create(['id' => $clientUser->id]);
        $ticket     = SupportTicket::factory()->create(['client_id' => $client->id]);

        $this->actingAs($admin, 'sanctum')
            ->postJson("/api/support-tickets/{$ticket->id}/assign", ['agent_id' => $admin->id])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('support_tickets', [
            'id'        => $ticket->id,
            'traite_par' => $admin->id,
            'statut'    => 'en_cours',
        ]);
    }

    public function test_client_cannot_assign_ticket(): void
    {
        $this->seed();

        $clientUser = $this->createUserWithRole('client');
        $client     = Client::factory()->create(['id' => $clientUser->id]);
        $ticket     = SupportTicket::factory()->create(['client_id' => $client->id]);

        $this->actingAs($clientUser, 'sanctum')
            ->postJson("/api/support-tickets/{$ticket->id}/assign", ['agent_id' => $clientUser->id])
            ->assertForbidden();
    }

    public function test_cannot_assign_closed_ticket(): void
    {
        $this->seed();

        $admin      = $this->createUserWithRole('admin');
        $clientUser = $this->createUserWithRole('client');
        $client     = Client::factory()->create(['id' => $clientUser->id]);
        $ticket     = SupportTicket::factory()->create(['client_id' => $client->id, 'statut' => 'ferme']);

        $this->actingAs($admin, 'sanctum')
            ->postJson("/api/support-tickets/{$ticket->id}/assign", ['agent_id' => $admin->id])
            ->assertStatus(422)
            ->assertJsonPath('success', false);
    }
}

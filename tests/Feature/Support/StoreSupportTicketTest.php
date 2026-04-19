<?php

namespace Tests\Feature\Support;

use App\Models\Client;
use App\Models\Reference\SupportCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithRoles;
use Tests\TestCase;

class StoreSupportTicketTest extends TestCase
{
    use RefreshDatabase, InteractsWithRoles;

    public function test_client_can_create_support_ticket(): void
    {
        $this->seed();

        $user     = $this->createUserWithRole('client');
        $client   = Client::factory()->create(['id' => $user->id]);
        $category = SupportCategory::firstOrFail();

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/support-tickets', [
                'client_id'           => $client->id,
                'support_category_id' => $category->id,
                'sujet'               => 'Problème de connexion',
                'description'         => "Je n'arrive pas à accéder à mon espace.",
            ])
            ->assertCreated()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('support_tickets', [
            'client_id' => $client->id,
            'sujet'     => 'Problème de connexion',
        ]);
    }

    public function test_ticket_creation_requires_required_fields(): void
    {
        $this->seed();

        $user = $this->createUserWithRole('client');

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/support-tickets', [])
            ->assertStatus(422)
            ->assertJsonPath('success', false);
    }
}

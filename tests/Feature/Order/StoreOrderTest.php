<?php

namespace Tests\Feature\Order;

use App\Models\Client;
use App\Models\Reference\OrderType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithRoles;
use Tests\TestCase;

class StoreOrderTest extends TestCase
{
    use RefreshDatabase, InteractsWithRoles;

    public function test_client_can_create_order(): void
    {
        $this->seed();

        $user   = $this->createUserWithRole('client');
        $client = Client::factory()->create(['id' => $user->id]);
        $type   = OrderType::firstOrFail();

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/orders', [
                'client_id'     => $client->id,
                'order_type_id' => $type->id,
                'description'   => "Commande d'intrants",
                'quantite'      => 10,
                'unite'         => 'kg',
            ])
            ->assertCreated()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('orders', [
            'client_id' => $client->id,
            'quantite'  => 10,
        ]);
    }
}

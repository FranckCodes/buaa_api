<?php

namespace Tests\Feature\Order;

use App\Models\Client;
use App\Models\Order;
use App\Models\Reference\OrderStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithRoles;
use Tests\TestCase;

class DeliverOrderTest extends TestCase
{
    use RefreshDatabase, InteractsWithRoles;

    public function test_admin_can_mark_order_as_delivered(): void
    {
        $this->seed();

        $admin          = $this->createUserWithRole('admin');
        $clientUser     = $this->createUserWithRole('client');
        $client         = Client::factory()->create(['id' => $clientUser->id]);
        $approuveStatus = OrderStatus::where('code', 'approuve')->firstOrFail();
        $order          = Order::factory()->create(['client_id' => $client->id, 'order_status_id' => $approuveStatus->id]);

        $this->actingAs($admin, 'sanctum')
            ->postJson("/api/orders/{$order->id}/deliver")
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertEquals(100, $order->fresh()->progression);
    }

    public function test_cannot_deliver_rejected_order(): void
    {
        $this->seed();

        $admin        = $this->createUserWithRole('admin');
        $clientUser   = $this->createUserWithRole('client');
        $client       = Client::factory()->create(['id' => $clientUser->id]);
        $rejeteStatus = OrderStatus::where('code', 'rejete')->firstOrFail();
        $order        = Order::factory()->create(['client_id' => $client->id, 'order_status_id' => $rejeteStatus->id]);

        $this->actingAs($admin, 'sanctum')
            ->postJson("/api/orders/{$order->id}/deliver")
            ->assertStatus(422)
            ->assertJsonPath('success', false);
    }
}

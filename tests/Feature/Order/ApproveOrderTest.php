<?php

namespace Tests\Feature\Order;

use App\Models\Client;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithRoles;
use Tests\TestCase;

class ApproveOrderTest extends TestCase
{
    use RefreshDatabase, InteractsWithRoles;

    public function test_admin_can_approve_order(): void
    {
        $this->seed();

        $admin      = $this->createUserWithRole('admin');
        $clientUser = $this->createUserWithRole('client');
        $client     = Client::factory()->create(['id' => $clientUser->id]);
        $order      = Order::factory()->create(['client_id' => $client->id]);

        $this->actingAs($admin, 'sanctum')
            ->postJson("/api/orders/{$order->id}/approve", ['processed_by' => $admin->id])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('order_tracking', ['order_id' => $order->id, 'ordre' => 1]);
    }

    public function test_client_cannot_approve_order(): void
    {
        $this->seed();

        $clientUser = $this->createUserWithRole('client');
        $client     = Client::factory()->create(['id' => $clientUser->id]);
        $order      = Order::factory()->create(['client_id' => $client->id]);

        $this->actingAs($clientUser, 'sanctum')
            ->postJson("/api/orders/{$order->id}/approve", ['processed_by' => $clientUser->id])
            ->assertForbidden();
    }
}

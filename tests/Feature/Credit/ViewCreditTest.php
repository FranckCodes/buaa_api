<?php

namespace Tests\Feature\Credit;

use App\Models\Client;
use App\Models\Credit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithRoles;
use Tests\TestCase;

class ViewCreditTest extends TestCase
{
    use RefreshDatabase, InteractsWithRoles;

    public function test_client_can_view_own_credit(): void
    {
        $this->seed();

        $user   = $this->createUserWithRole('client');
        $client = Client::factory()->create(['id' => $user->id]);
        $credit = Credit::factory()->create(['client_id' => $client->id]);

        $this->actingAs($user, 'sanctum')
            ->getJson("/api/credits/{$credit->id}")
            ->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_client_cannot_view_other_client_credit(): void
    {
        $this->seed();

        $userA   = $this->createUserWithRole('client');
        $clientA = Client::factory()->create(['id' => $userA->id]);

        $userB   = $this->createUserWithRole('client');
        $clientB = Client::factory()->create(['id' => $userB->id]);
        $credit  = Credit::factory()->create(['client_id' => $clientB->id]);

        $this->actingAs($userA, 'sanctum')
            ->getJson("/api/credits/{$credit->id}")
            ->assertForbidden();
    }

    public function test_admin_can_view_any_credit(): void
    {
        $this->seed();

        $admin      = $this->createUserWithRole('admin');
        $clientUser = $this->createUserWithRole('client');
        $client     = Client::factory()->create(['id' => $clientUser->id]);
        $credit     = Credit::factory()->create(['client_id' => $client->id]);

        $this->actingAs($admin, 'sanctum')
            ->getJson("/api/credits/{$credit->id}")
            ->assertOk();
    }
}

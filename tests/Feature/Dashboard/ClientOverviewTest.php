<?php

namespace Tests\Feature\Dashboard;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithRoles;
use Tests\TestCase;

class ClientOverviewTest extends TestCase
{
    use RefreshDatabase, InteractsWithRoles;

    public function test_client_can_access_own_dashboard(): void
    {
        $this->seed();

        $client = $this->createUserWithRole('client');

        $this->actingAs($client, 'sanctum')
            ->getJson('/api/dashboard/client/overview')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => ['credits', 'orders', 'reports', 'insurances', 'adhesions', 'support', 'notifications'],
            ]);
    }

    public function test_admin_cannot_access_client_dashboard(): void
    {
        $this->seed();

        $admin = $this->createUserWithRole('admin');

        $this->actingAs($admin, 'sanctum')
            ->getJson('/api/dashboard/client/overview')
            ->assertForbidden();
    }
}

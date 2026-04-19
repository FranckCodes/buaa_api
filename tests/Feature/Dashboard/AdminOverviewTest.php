<?php

namespace Tests\Feature\Dashboard;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithRoles;
use Tests\TestCase;

class AdminOverviewTest extends TestCase
{
    use RefreshDatabase, InteractsWithRoles;

    public function test_admin_can_access_admin_overview(): void
    {
        $this->seed();

        $admin = $this->createUserWithRole('admin');

        $this->actingAs($admin, 'sanctum')
            ->getJson('/api/dashboard/admin/overview')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success', 'message',
                'data' => [
                    'users', 'clients', 'credits', 'orders',
                    'reports', 'insurances', 'adhesions', 'support', 'feed', 'notifications',
                ],
            ]);
    }

    public function test_client_cannot_access_admin_overview(): void
    {
        $this->seed();

        $client = $this->createUserWithRole('client');

        $this->actingAs($client, 'sanctum')
            ->getJson('/api/dashboard/admin/overview')
            ->assertForbidden();
    }

    public function test_admin_can_access_trends(): void
    {
        $this->seed();

        $admin = $this->createUserWithRole('admin');

        $this->actingAs($admin, 'sanctum')
            ->getJson('/api/dashboard/admin/trends?months=3')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => ['credits', 'orders', 'reports', 'posts', 'support_tickets', 'insurance_claims'],
            ]);
    }

    public function test_admin_can_access_kpis(): void
    {
        $this->seed();

        $admin = $this->createUserWithRole('admin');

        $this->actingAs($admin, 'sanctum')
            ->getJson('/api/dashboard/admin/kpis')
            ->assertOk()
            ->assertJsonPath('success', true);
    }
}

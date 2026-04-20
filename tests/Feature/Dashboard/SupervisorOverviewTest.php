<?php

namespace Tests\Feature\Dashboard;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithRoles;
use Tests\TestCase;

class SupervisorOverviewTest extends TestCase
{
    use RefreshDatabase, InteractsWithRoles;

    public function test_supervisor_can_access_supervisor_overview(): void
    {
        $this->seed();

        $supervisor = $this->createUserWithRole('superviseur');

        $this->actingAs($supervisor, 'sanctum')
            ->getJson('/api/dashboard/supervisor/overview')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => ['clients', 'credits', 'orders', 'reports', 'support'],
            ]);
    }

    public function test_client_cannot_access_supervisor_dashboard(): void
    {
        $this->seed();

        $client = $this->createUserWithRole('client');

        $this->actingAs($client, 'sanctum')
            ->getJson('/api/dashboard/supervisor/overview')
            ->assertForbidden();
    }
}

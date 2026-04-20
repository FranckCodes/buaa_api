<?php

namespace Tests\Feature\Report;

use App\Models\Client;
use App\Models\Reference\ReportType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithRoles;
use Tests\TestCase;

class StoreReportTest extends TestCase
{
    use RefreshDatabase, InteractsWithRoles;

    public function test_client_can_create_report(): void
    {
        $this->seed();

        $user   = $this->createUserWithRole('client');
        $client = Client::factory()->create(['id' => $user->id]);
        $type   = ReportType::firstOrFail();

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/reports', [
                'client_id'      => $client->id,
                'report_type_id' => $type->id,
                'summary'        => 'Rapport hebdomadaire',
                'value_numeric'  => 120,
                'value_unit'     => 'kg',
                'date_rapport'   => now()->toDateString(),
            ])
            ->assertCreated()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('reports', [
            'client_id' => $client->id,
            'summary'   => 'Rapport hebdomadaire',
        ]);
    }

    public function test_report_creation_requires_required_fields(): void
    {
        $this->seed();

        $user = $this->createUserWithRole('client');

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/reports', [])
            ->assertStatus(422)
            ->assertJsonPath('success', false);
    }
}

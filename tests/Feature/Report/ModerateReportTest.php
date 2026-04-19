<?php

namespace Tests\Feature\Report;

use App\Models\Client;
use App\Models\Report;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithRoles;
use Tests\TestCase;

class ModerateReportTest extends TestCase
{
    use RefreshDatabase, InteractsWithRoles;

    public function test_admin_can_validate_report(): void
    {
        $this->seed();

        $admin      = $this->createUserWithRole('admin');
        $clientUser = $this->createUserWithRole('client');
        $client     = Client::factory()->create(['id' => $clientUser->id]);
        $report     = Report::factory()->create(['client_id' => $client->id]);

        $this->actingAs($admin, 'sanctum')
            ->postJson("/api/reports/{$report->id}/moderate", [
                'action'       => 'validate',
                'validator_id' => $admin->id,
            ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertEquals('validated', $report->fresh()->status->code);
    }

    public function test_admin_can_request_revision(): void
    {
        $this->seed();

        $admin      = $this->createUserWithRole('admin');
        $clientUser = $this->createUserWithRole('client');
        $client     = Client::factory()->create(['id' => $clientUser->id]);
        $report     = Report::factory()->create(['client_id' => $client->id]);

        $this->actingAs($admin, 'sanctum')
            ->postJson("/api/reports/{$report->id}/moderate", [
                'action'       => 'revision',
                'validator_id' => $admin->id,
                'reason'       => 'Merci de préciser la valeur totale.',
            ])
            ->assertOk();

        $this->assertEquals('revision', $report->fresh()->status->code);
    }

    public function test_client_cannot_moderate_report(): void
    {
        $this->seed();

        $clientUser = $this->createUserWithRole('client');
        $client     = Client::factory()->create(['id' => $clientUser->id]);
        $report     = Report::factory()->create(['client_id' => $client->id]);

        $this->actingAs($clientUser, 'sanctum')
            ->postJson("/api/reports/{$report->id}/moderate", [
                'action'       => 'validate',
                'validator_id' => $clientUser->id,
            ])
            ->assertForbidden();
    }

    public function test_cannot_validate_already_validated_report(): void
    {
        $this->seed();

        $admin      = $this->createUserWithRole('admin');
        $clientUser = $this->createUserWithRole('client');
        $client     = Client::factory()->create(['id' => $clientUser->id]);
        $report     = Report::factory()->create(['client_id' => $client->id]);

        // Première validation
        $this->actingAs($admin, 'sanctum')
            ->postJson("/api/reports/{$report->id}/moderate", [
                'action'       => 'validate',
                'validator_id' => $admin->id,
            ]);

        // Deuxième validation — doit échouer
        $this->actingAs($admin, 'sanctum')
            ->postJson("/api/reports/{$report->id}/moderate", [
                'action'       => 'validate',
                'validator_id' => $admin->id,
            ])
            ->assertStatus(422)
            ->assertJsonPath('success', false);
    }
}

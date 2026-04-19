<?php

namespace Tests\Feature\Adhesion;

use App\Models\AdhesionRequest;
use App\Models\Client;
use App\Models\Reference\AdhesionType;
use App\Models\Reference\PaymentMode;
use App\Models\Union;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithRoles;
use Tests\TestCase;

class ApproveAdhesionRequestTest extends TestCase
{
    use RefreshDatabase, InteractsWithRoles;

    public function test_admin_can_approve_adhesion_request(): void
    {
        $this->seed();

        $admin        = $this->createUserWithRole('admin');
        $clientUser   = $this->createUserWithRole('client');
        $client       = Client::factory()->create(['id' => $clientUser->id]);
        $union        = Union::factory()->create();
        $requestModel = AdhesionRequest::factory()->create();
        $type         = AdhesionType::firstOrFail();
        $paymentMode  = PaymentMode::firstOrFail();

        $this->actingAs($admin, 'sanctum')
            ->postJson("/api/adhesion-requests/{$requestModel->id}/approve", [
                'client_id'        => $client->id,
                'union_id'         => $union->id,
                'adhesion_type_id' => $type->id,
                'payment_mode_id'  => $paymentMode->id,
                'processed_by'     => $admin->id,
            ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('adhesions', ['client_id' => $client->id, 'union_id' => $union->id]);
        $this->assertDatabaseHas('cotisations', ['annee' => now()->year]);
    }

    public function test_client_cannot_approve_adhesion_request(): void
    {
        $this->seed();

        $clientUser   = $this->createUserWithRole('client');
        $client       = Client::factory()->create(['id' => $clientUser->id]);
        $union        = Union::factory()->create();
        $requestModel = AdhesionRequest::factory()->create();

        $this->actingAs($clientUser, 'sanctum')
            ->postJson("/api/adhesion-requests/{$requestModel->id}/approve", [
                'client_id'        => $client->id,
                'union_id'         => $union->id,
                'adhesion_type_id' => AdhesionType::firstOrFail()->id,
                'processed_by'     => $clientUser->id,
            ])
            ->assertForbidden();
    }
}

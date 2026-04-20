<?php

namespace Tests\Unit\Services;

use App\Models\Client;
use App\Models\Credit;
use App\Models\Reference\CreditType;
use App\Services\CreditService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreditServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_credit_request(): void
    {
        $this->seed();

        $service    = app(CreditService::class);
        $client     = Client::factory()->create();
        $creditType = CreditType::firstOrFail();

        $credit = $service->createCreditRequest([
            'client_id'       => $client->id,
            'credit_type_id'  => $creditType->id,
            'montant_demande' => 700,
            'duree_mois'      => 7,
        ]);

        $this->assertInstanceOf(Credit::class, $credit);
        $this->assertEquals(700, (float) $credit->montant_demande);
    }

    public function test_it_generates_repayment_schedule_on_approval(): void
    {
        $this->seed();

        $service = app(CreditService::class);
        $client  = Client::factory()->create();
        $credit  = Credit::factory()->create(['client_id' => $client->id, 'duree_mois' => 4]);
        $admin   = \App\Models\User::factory()->create();

        $service->approveCredit($credit, [
            'montant_approuve' => 400,
            'montant_echeance' => 100,
            'traite_par'       => $admin->id,
        ]);

        $this->assertDatabaseCount('credit_payments', 4);
    }
}

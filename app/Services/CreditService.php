<?php

namespace App\Services;

use App\Exceptions\BusinessException;
use App\Models\Credit;
use App\Models\CreditPayment;
use App\Models\Reference\CreditStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CreditService
{
    public function __construct(
        protected IdGeneratorService $idGenerator,
        protected NotificationService $notificationService
    ) {}

    public function createCreditRequest(array $data): Credit
    {
        $status = CreditStatus::where('code', 'en_analyse')->firstOrFail();

        return Credit::create([
            'id'                     => $this->idGenerator->generateCreditId(),
            'client_id'              => $data['client_id'],
            'credit_type_id'         => $data['credit_type_id'],
            'credit_status_id'       => $status->id,
            'montant_demande'        => $data['montant_demande'],
            'date_demande'           => $data['date_demande'] ?? now()->toDateString(),
            'duree_mois'             => $data['duree_mois'],
            'taux_interet'           => $data['taux_interet'] ?? null,
            'objet_credit'           => $data['objet_credit'] ?? null,
            'description_projet'     => $data['description_projet'] ?? null,
            'retour_investissement'  => $data['retour_investissement'] ?? null,
            'revenus_mensuels'       => $data['revenus_mensuels'] ?? null,
            'autres_credits'         => $data['autres_credits'] ?? false,
            'montant_autres_credits' => $data['montant_autres_credits'] ?? null,
        ]);
    }

    public function approveCredit(Credit $credit, array $decisionData): Credit
    {
        if ($credit->status?->code === 'actif') {
            throw new BusinessException('Ce crédit est déjà approuvé.', 422);
        }

        return DB::transaction(function () use ($credit, $decisionData) {
            $status = CreditStatus::where('code', 'actif')->firstOrFail();

            $credit->update([
                'credit_status_id'   => $status->id,
                'montant_approuve'   => $decisionData['montant_approuve'],
                'date_approbation'   => now()->toDateString(),
                'montant_echeance'   => $decisionData['montant_echeance'] ?? null,
                'prochaine_echeance' => $decisionData['prochaine_echeance'] ?? now()->addMonth()->toDateString(),
                'traite_par'         => $decisionData['traite_par'],
            ]);

            $this->generateRepaymentSchedule($credit);

            $this->notificationService->create([
                'user_id'  => $credit->client_id,
                'category' => 'app',
                'type'     => 'credit',
                'title'    => 'Crédit approuvé',
                'body'     => 'Votre demande de crédit a été approuvée.',
            ]);

            return $credit->fresh(['status', 'payments', 'type', 'client']);
        });
    }

    public function rejectCredit(Credit $credit, string $processedBy): Credit
    {
        if ($credit->status?->code === 'rejete') {
            throw new BusinessException('Ce crédit est déjà rejeté.', 422);
        }

        $status = CreditStatus::where('code', 'rejete')->firstOrFail();

        $credit->update([
            'credit_status_id' => $status->id,
            'traite_par'       => $processedBy,
        ]);

        $this->notificationService->create([
            'user_id'  => $credit->client_id,
            'category' => 'app',
            'type'     => 'credit',
            'title'    => 'Crédit rejeté',
            'body'     => 'Votre demande de crédit a été rejetée.',
        ]);

        return $credit->fresh('status');
    }

    public function generateRepaymentSchedule(Credit $credit): void
    {
        if (!$credit->montant_approuve || !$credit->duree_mois) {
            return;
        }

        $monthlyAmount = round($credit->montant_approuve / $credit->duree_mois, 2);
        $startDate = Carbon::parse($credit->date_approbation ?? now());

        for ($i = 1; $i <= $credit->duree_mois; $i++) {
            $dueDate = $startDate->copy()->addMonths($i);

            CreditPayment::updateOrCreate(
                [
                    'credit_id'    => $credit->id,
                    'periode_annee' => $dueDate->year,
                    'periode_mois' => $dueDate->month,
                ],
                [
                    'montant'       => $monthlyAmount,
                    'statut'        => 'en_attente',
                    'date_echeance' => $dueDate->toDateString(),
                ]
            );
        }
    }

    public function registerPayment(CreditPayment $payment, array $data): CreditPayment
    {
        if ($payment->statut === 'paye') {
            throw new BusinessException('Cette échéance est déjà payée.', 422);
        }

        return DB::transaction(function () use ($payment, $data) {
            $payment->update([
                'statut'        => 'paye',
                'date_paiement' => $data['date_paiement'] ?? now()->toDateString(),
            ]);

            $credit = $payment->credit;
            $totalPaid = $credit->payments()->where('statut', 'paye')->sum('montant');
            $credit->update(['montant_rembourse' => $totalPaid]);

            $this->refreshCreditStatus($credit);

            return $payment->fresh();
        });
    }

    public function refreshCreditStatus(Credit $credit): Credit
    {
        $pendingCount = $credit->payments()->where('statut', '!=', 'paye')->count();

        if ($pendingCount === 0) {
            $status = CreditStatus::where('code', 'rembourse')->firstOrFail();
            $credit->update(['credit_status_id' => $status->id]);
        }

        return $credit->fresh('status');
    }
}

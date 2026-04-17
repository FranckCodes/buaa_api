<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Credit\ApproveCreditRequest;
use App\Http\Requests\Credit\RegisterCreditPaymentRequest;
use App\Http\Requests\Credit\StoreCreditRequest;
use App\Http\Resources\CreditResource;
use App\Models\Credit;
use App\Models\CreditPayment;
use App\Services\CreditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CreditController extends Controller
{
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Credit::class);

        return $this->paginatedResponse(
            Credit::with(['client.user', 'type', 'status'])->latest()->paginate(15),
            'Liste des crédits récupérée avec succès.',
            fn ($credit) => new CreditResource($credit)
        );
    }

    public function store(StoreCreditRequest $request, CreditService $creditService): JsonResponse
    {
        $this->authorize('create', Credit::class);
        $credit = $creditService->createCreditRequest($request->validated());

        return $this->successResponse(
            new CreditResource($credit->load(['client.user', 'type', 'status'])),
            'Demande de crédit créée avec succès.',
            201
        );
    }

    public function show(Credit $credit): JsonResponse
    {
        $this->authorize('view', $credit);

        return $this->successResponse(
            new CreditResource($credit->load(['client.user', 'type', 'status', 'treatedBy', 'payments', 'businessPlan', 'documents'])),
            'Détail du crédit récupéré avec succès.'
        );
    }

    public function approve(ApproveCreditRequest $request, Credit $credit, CreditService $creditService): JsonResponse
    {
        $this->authorize('approve', $credit);

        return $this->successResponse(
            new CreditResource($creditService->approveCredit($credit, $request->validated())),
            'Crédit approuvé avec succès.'
        );
    }

    public function reject(Request $request, Credit $credit, CreditService $creditService): JsonResponse
    {
        $this->authorize('reject', $credit);
        $request->validate(['processed_by' => ['required', 'integer', 'exists:users,id']]);

        return $this->successResponse(
            new CreditResource($creditService->rejectCredit($credit, $request->integer('processed_by'))),
            'Crédit rejeté.'
        );
    }

    public function registerPayment(RegisterCreditPaymentRequest $request, CreditPayment $payment, CreditService $creditService): JsonResponse
    {
        $this->authorize('registerPayment', $payment->credit);

        return $this->successResponse(
            $creditService->registerPayment($payment, $request->validated()),
            'Paiement enregistré avec succès.'
        );
    }
}

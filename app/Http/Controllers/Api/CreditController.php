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
        return CreditResource::collection(
            Credit::with(['client.user', 'type', 'status'])->latest()->paginate(15)
        )->additional(['message' => 'Liste des crédits.'])->response();
    }

    public function store(StoreCreditRequest $request, CreditService $creditService): JsonResponse
    {
        $credit = $creditService->createCreditRequest($request->validated());

        return response()->json([
            'message' => 'Demande de crédit créée avec succès.',
            'data'    => new CreditResource($credit->load(['client.user', 'type', 'status'])),
        ], 201);
    }

    public function show(Credit $credit): JsonResponse
    {
        return response()->json([
            'message' => 'Détail du crédit.',
            'data'    => new CreditResource($credit->load(['client.user', 'type', 'status', 'treatedBy', 'payments', 'businessPlan', 'documents'])),
        ]);
    }

    public function approve(ApproveCreditRequest $request, Credit $credit, CreditService $creditService): JsonResponse
    {
        $this->authorize('approve', $credit);
        $result = $creditService->approveCredit($credit, $request->validated());

        return response()->json(['message' => 'Crédit approuvé avec succès.', 'data' => new CreditResource($result)]);
    }

    public function reject(Request $request, Credit $credit, CreditService $creditService): JsonResponse
    {
        $this->authorize('reject', $credit);
        $request->validate(['processed_by' => ['required', 'integer', 'exists:users,id']]);
        $result = $creditService->rejectCredit($credit, $request->integer('processed_by'));

        return response()->json(['message' => 'Crédit rejeté.', 'data' => new CreditResource($result)]);
    }

    public function registerPayment(RegisterCreditPaymentRequest $request, CreditPayment $payment, CreditService $creditService): JsonResponse
    {
        $this->authorize('registerPayment', $payment->credit);
        $result = $creditService->registerPayment($payment, $request->validated());

        return response()->json(['message' => 'Paiement enregistré avec succès.', 'data' => $result]);
    }
}

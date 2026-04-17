<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Credit\ApproveCreditRequest;
use App\Http\Requests\Credit\RegisterCreditPaymentRequest;
use App\Http\Requests\Credit\StoreCreditRequest;
use App\Models\Credit;
use App\Models\CreditPayment;
use App\Services\CreditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CreditController extends Controller
{
    public function index(): JsonResponse
    {
        $credits = Credit::with(['client.user', 'type', 'status', 'treatedBy'])
            ->latest()->paginate(15);

        return response()->json(['message' => 'Liste des crédits.', 'data' => $credits]);
    }

    public function store(StoreCreditRequest $request, CreditService $creditService): JsonResponse
    {
        $credit = $creditService->createCreditRequest($request->validated());

        return response()->json([
            'message' => 'Demande de crédit créée avec succès.',
            'data'    => $credit->load(['client.user', 'type', 'status']),
        ], 201);
    }

    public function show(Credit $credit): JsonResponse
    {
        $credit->load(['client.user', 'type', 'status', 'treatedBy', 'payments', 'businessPlan', 'documents']);

        return response()->json(['message' => 'Détail du crédit.', 'data' => $credit]);
    }

    public function approve(ApproveCreditRequest $request, Credit $credit, CreditService $creditService): JsonResponse
    {
        $result = $creditService->approveCredit($credit, $request->validated());

        return response()->json(['message' => 'Crédit approuvé avec succès.', 'data' => $result]);
    }

    public function reject(Request $request, Credit $credit, CreditService $creditService): JsonResponse
    {
        $request->validate(['processed_by' => ['required', 'integer', 'exists:users,id']]);

        $result = $creditService->rejectCredit($credit, $request->integer('processed_by'));

        return response()->json(['message' => 'Crédit rejeté.', 'data' => $result]);
    }

    public function registerPayment(RegisterCreditPaymentRequest $request, CreditPayment $payment, CreditService $creditService): JsonResponse
    {
        $result = $creditService->registerPayment($payment, $request->validated());

        return response()->json(['message' => 'Paiement enregistré avec succès.', 'data' => $result]);
    }
}

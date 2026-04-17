<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Insurance\ActivateInsuranceRequest;
use App\Http\Requests\Insurance\ApproveInsuranceClaimRequest;
use App\Http\Requests\Insurance\StoreInsuranceClaimRequest;
use App\Http\Requests\Insurance\StoreInsuranceRequest;
use App\Models\Insurance;
use App\Models\InsuranceClaim;
use App\Services\InsuranceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InsuranceController extends Controller
{
    public function index(): JsonResponse
    {
        $items = Insurance::with(['client.user', 'type', 'status', 'treatedBy'])
            ->latest()->paginate(15);

        return response()->json(['message' => 'Liste des assurances.', 'data' => $items]);
    }

    public function store(StoreInsuranceRequest $request, InsuranceService $insuranceService): JsonResponse
    {
        $insurance = $insuranceService->createSubscription($request->validated());

        return response()->json([
            'message' => 'Souscription enregistrée avec succès.',
            'data'    => $insurance->load(['client.user', 'type', 'status']),
        ], 201);
    }

    public function show(Insurance $insurance): JsonResponse
    {
        $insurance->load(['client.user', 'type', 'status', 'treatedBy', 'beneficiaries', 'claims', 'documents']);

        return response()->json(["message" => "Détail de l'assurance.", 'data' => $insurance]);
    }

    public function activate(ActivateInsuranceRequest $request, Insurance $insurance, InsuranceService $insuranceService): JsonResponse
    {
        $result = $insuranceService->activateInsurance($insurance, $request->integer('processed_by'));

        return response()->json(['message' => 'Assurance activée avec succès.', 'data' => $result]);
    }

    public function claimsIndex(): JsonResponse
    {
        $items = InsuranceClaim::with(['insurance', 'client.user', 'treatedBy'])
            ->latest()->paginate(15);

        return response()->json(['message' => 'Liste des réclamations.', 'data' => $items]);
    }

    public function storeClaim(StoreInsuranceClaimRequest $request, InsuranceService $insuranceService): JsonResponse
    {
        $claim = $insuranceService->createClaim($request->validated());

        return response()->json(['message' => 'Réclamation créée avec succès.', 'data' => $claim], 201);
    }

    public function approveClaim(ApproveInsuranceClaimRequest $request, InsuranceClaim $claim, InsuranceService $insuranceService): JsonResponse
    {
        $result = $insuranceService->approveClaim($claim, (float) $request->input('amount'), $request->integer('processed_by'));

        return response()->json(['message' => 'Réclamation approuvée avec succès.', 'data' => $result]);
    }

    public function rejectClaim(Request $request, InsuranceClaim $claim, InsuranceService $insuranceService): JsonResponse
    {
        $request->validate(['processed_by' => ['required', 'integer', 'exists:users,id']]);

        $result = $insuranceService->rejectClaim($claim, $request->integer('processed_by'));

        return response()->json(['message' => 'Réclamation rejetée.', 'data' => $result]);
    }
}

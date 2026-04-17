<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Insurance\ActivateInsuranceRequest;
use App\Http\Requests\Insurance\ApproveInsuranceClaimRequest;
use App\Http\Requests\Insurance\StoreInsuranceClaimRequest;
use App\Http\Requests\Insurance\StoreInsuranceRequest;
use App\Http\Resources\InsuranceClaimResource;
use App\Http\Resources\InsuranceResource;
use App\Models\Insurance;
use App\Models\InsuranceClaim;
use App\Services\InsuranceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InsuranceController extends Controller
{
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Insurance::class);

        return InsuranceResource::collection(
            Insurance::with(['client.user', 'type', 'status'])->latest()->paginate(15)
        )->additional(['message' => 'Liste des assurances.'])->response();
    }

    public function store(StoreInsuranceRequest $request, InsuranceService $insuranceService): JsonResponse
    {
        $insurance = $insuranceService->createSubscription($request->validated());

        return response()->json([
            'message' => 'Souscription enregistrée avec succès.',
            'data'    => new InsuranceResource($insurance->load(['client.user', 'type', 'status'])),
        ], 201);
    }

    public function show(Insurance $insurance): JsonResponse
    {
        $this->authorize('view', $insurance);

        return response()->json([
            "message" => "Détail de l'assurance.",
            'data'    => new InsuranceResource($insurance->load(['client.user', 'type', 'status', 'treatedBy', 'beneficiaries', 'claims', 'documents'])),
        ]);
    }

    public function activate(ActivateInsuranceRequest $request, Insurance $insurance, InsuranceService $insuranceService): JsonResponse
    {
        $this->authorize('activate', $insurance);
        $result = $insuranceService->activateInsurance($insurance, $request->integer('processed_by'));

        return response()->json(['message' => 'Assurance activée avec succès.', 'data' => new InsuranceResource($result)]);
    }

    public function claimsIndex(): JsonResponse
    {
        return InsuranceClaimResource::collection(
            InsuranceClaim::with(['insurance', 'client.user', 'treatedBy'])->latest()->paginate(15)
        )->additional(['message' => 'Liste des réclamations.'])->response();
    }

    public function storeClaim(StoreInsuranceClaimRequest $request, InsuranceService $insuranceService): JsonResponse
    {
        $claim = $insuranceService->createClaim($request->validated());

        return response()->json(['message' => 'Réclamation créée avec succès.', 'data' => new InsuranceClaimResource($claim)], 201);
    }

    public function approveClaim(ApproveInsuranceClaimRequest $request, InsuranceClaim $claim, InsuranceService $insuranceService): JsonResponse
    {
        $result = $insuranceService->approveClaim($claim, (float) $request->input('amount'), $request->integer('processed_by'));

        return response()->json(['message' => 'Réclamation approuvée avec succès.', 'data' => new InsuranceClaimResource($result)]);
    }

    public function rejectClaim(Request $request, InsuranceClaim $claim, InsuranceService $insuranceService): JsonResponse
    {
        $request->validate(['processed_by' => ['required', 'integer', 'exists:users,id']]);
        $result = $insuranceService->rejectClaim($claim, $request->integer('processed_by'));

        return response()->json(['message' => 'Réclamation rejetée.', 'data' => new InsuranceClaimResource($result)]);
    }
}

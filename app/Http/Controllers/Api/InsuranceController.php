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

        return $this->paginatedResponse(
            Insurance::with(['client.user', 'type', 'status', 'treatedBy'])->latest()->paginate(15),
            'Liste des assurances récupérée avec succès.',
            fn ($insurance) => new InsuranceResource($insurance)
        );
    }

    public function store(StoreInsuranceRequest $request, InsuranceService $insuranceService): JsonResponse
    {
        $this->authorize('create', Insurance::class);

        $insurance = $insuranceService->createSubscription($request->validated());

        return $this->successResponse(
            new InsuranceResource($insurance->load(['client.user', 'type', 'status', 'beneficiaries'])),
            'Souscription enregistrée avec succès.',
            201
        );
    }

    public function show(Insurance $insurance): JsonResponse
    {
        $this->authorize('view', $insurance);

        return $this->successResponse(
            new InsuranceResource($insurance->load(['client.user', 'type', 'status', 'treatedBy', 'beneficiaries', 'claims', 'documents'])),
            "Détail de l'assurance récupéré avec succès."
        );
    }

    public function activate(ActivateInsuranceRequest $request, Insurance $insurance, InsuranceService $insuranceService): JsonResponse
    {
        $this->authorize('activate', $insurance);

        return $this->successResponse(
            new InsuranceResource($insuranceService->activateInsurance($insurance, $request->string('processed_by')->toString())),
            'Assurance activée avec succès.'
        );
    }

    public function claimsIndex(): JsonResponse
    {
        return $this->paginatedResponse(
            InsuranceClaim::with(['insurance', 'client.user', 'treatedBy'])->latest()->paginate(15),
            'Liste des réclamations récupérée avec succès.',
            fn ($claim) => new InsuranceClaimResource($claim)
        );
    }

    public function storeClaim(StoreInsuranceClaimRequest $request, InsuranceService $insuranceService): JsonResponse
    {
        $claim = $insuranceService->createClaim($request->validated());

        return $this->successResponse(
            new InsuranceClaimResource($claim->load(['insurance', 'client.user'])),
            'Réclamation créée avec succès.',
            201
        );
    }

    public function approveClaim(ApproveInsuranceClaimRequest $request, InsuranceClaim $claim, InsuranceService $insuranceService): JsonResponse
    {
        $this->authorize('manageClaims', $claim->insurance);

        return $this->successResponse(
            new InsuranceClaimResource($insuranceService->approveClaim($claim, (float) $request->input('amount'), $request->string('processed_by')->toString())),
            'Réclamation approuvée avec succès.'
        );
    }

    public function rejectClaim(Request $request, InsuranceClaim $claim, InsuranceService $insuranceService): JsonResponse
    {
        $this->authorize('manageClaims', $claim->insurance);
        $request->validate(['processed_by' => ['required', 'string', 'exists:users,id']]);

        return $this->successResponse(
            new InsuranceClaimResource($insuranceService->rejectClaim($claim, $request->string('processed_by')->toString())),
            'Réclamation rejetée avec succès.'
        );
    }
}

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
            Insurance::with(['client.user', 'type', 'status'])->latest()->paginate(15),
            'Liste des assurances récupérée avec succès.',
            fn ($item) => new InsuranceResource($item)
        );
    }

    public function store(StoreInsuranceRequest $request, InsuranceService $insuranceService): JsonResponse
    {
        $this->authorize('create', Insurance::class);

        return $this->successResponse(
            new InsuranceResource($insuranceService->createSubscription($request->validated())->load(['client.user', 'type', 'status'])),
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
            new InsuranceResource($insuranceService->activateInsurance($insurance, $request->integer('processed_by'))),
            'Assurance activée avec succès.'
        );
    }

    public function claimsIndex(): JsonResponse
    {
        return $this->paginatedResponse(
            InsuranceClaim::with(['insurance', 'client.user', 'treatedBy'])->latest()->paginate(15),
            'Liste des réclamations récupérée avec succès.',
            fn ($item) => new InsuranceClaimResource($item)
        );
    }

    public function storeClaim(StoreInsuranceClaimRequest $request, InsuranceService $insuranceService): JsonResponse
    {
        return $this->successResponse(
            new InsuranceClaimResource($insuranceService->createClaim($request->validated())),
            'Réclamation créée avec succès.',
            201
        );
    }

    public function approveClaim(ApproveInsuranceClaimRequest $request, InsuranceClaim $claim, InsuranceService $insuranceService): JsonResponse
    {
        return $this->successResponse(
            new InsuranceClaimResource($insuranceService->approveClaim($claim, (float) $request->input('amount'), $request->integer('processed_by'))),
            'Réclamation approuvée avec succès.'
        );
    }

    public function rejectClaim(Request $request, InsuranceClaim $claim, InsuranceService $insuranceService): JsonResponse
    {
        $request->validate(['processed_by' => ['required', 'integer', 'exists:users,id']]);

        return $this->successResponse(
            new InsuranceClaimResource($insuranceService->rejectClaim($claim, $request->integer('processed_by'))),
            'Réclamation rejetée.'
        );
    }
}

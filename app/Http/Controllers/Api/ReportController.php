<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Report\ModerateReportRequest;
use App\Http\Requests\Report\StoreReportRequest;
use App\Http\Resources\ReportResource;
use App\Models\Report;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Report::class);

        return $this->paginatedResponse(
            Report::with(['client.user', 'superviseur', 'type', 'status'])->latest()->paginate(15),
            'Liste des rapports récupérée avec succès.',
            fn ($item) => new ReportResource($item)
        );
    }

    public function store(StoreReportRequest $request, ReportService $reportService): JsonResponse
    {
        $this->authorize('create', Report::class);

        return $this->successResponse(
            new ReportResource($reportService->createReport($request->validated())->load(['client.user', 'type', 'status'])),
            'Rapport créé avec succès.',
            201
        );
    }

    public function show(Report $report): JsonResponse
    {
        $this->authorize('view', $report);

        return $this->successResponse(
            new ReportResource($report->load(['client.user', 'superviseur', 'type', 'status', 'validatedBy', 'documents'])),
            'Détail du rapport récupéré avec succès.'
        );
    }

    public function moderate(ModerateReportRequest $request, Report $report, ReportService $reportService): JsonResponse
    {
        $this->authorize('moderate', $report);
        $data = $request->validated();

        $result = match ($data['action']) {
            'validate' => $reportService->validateReport($report, $data['validator_id']),
            'revision' => $reportService->requestRevision($report, $data['validator_id'], $data['reason'] ?? null),
            'reject'   => $reportService->rejectReport($report, $data['validator_id'], $data['reason'] ?? null),
        };

        return $this->successResponse(new ReportResource($result), 'Action sur le rapport effectuée avec succès.');
    }
}

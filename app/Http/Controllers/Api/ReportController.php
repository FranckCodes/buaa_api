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

        return ReportResource::collection(
            Report::with(['client.user', 'superviseur', 'type', 'status'])->latest()->paginate(15)
        )->additional(['message' => 'Liste des rapports.'])->response();
    }

    public function store(StoreReportRequest $request, ReportService $reportService): JsonResponse
    {
        $this->authorize('create', Report::class);
        $report = $reportService->createReport($request->validated());

        return response()->json([
            'message' => 'Rapport créé avec succès.',
            'data'    => new ReportResource($report->load(['client.user', 'type', 'status'])),
        ], 201);
    }

    public function show(Report $report): JsonResponse
    {
        $this->authorize('view', $report);

        return response()->json([
            'message' => 'Détail du rapport.',
            'data'    => new ReportResource($report->load(['client.user', 'superviseur', 'type', 'status', 'validatedBy', 'documents'])),
        ]);
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

        return response()->json(['message' => 'Action sur le rapport effectuée avec succès.', 'data' => new ReportResource($result)]);
    }
}

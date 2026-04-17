<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Report\ModerateReportRequest;
use App\Http\Requests\Report\StoreReportRequest;
use App\Models\Report;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    public function index(): JsonResponse
    {
        $items = Report::with(['client.user', 'superviseur', 'type', 'status', 'validatedBy'])
            ->latest()->paginate(15);

        return response()->json(['message' => 'Liste des rapports.', 'data' => $items]);
    }

    public function store(StoreReportRequest $request, ReportService $reportService): JsonResponse
    {
        $report = $reportService->createReport($request->validated());

        return response()->json([
            'message' => 'Rapport créé avec succès.',
            'data'    => $report->load(['client.user', 'type', 'status']),
        ], 201);
    }

    public function show(Report $report): JsonResponse
    {
        $report->load(['client.user', 'superviseur', 'type', 'status', 'validatedBy', 'documents']);

        return response()->json(['message' => 'Détail du rapport.', 'data' => $report]);
    }

    public function moderate(ModerateReportRequest $request, Report $report, ReportService $reportService): JsonResponse
    {
        $data = $request->validated();

        $result = match ($data['action']) {
            'validate' => $reportService->validateReport($report, $data['validator_id']),
            'revision' => $reportService->requestRevision($report, $data['validator_id'], $data['reason'] ?? null),
            'reject'   => $reportService->rejectReport($report, $data['validator_id'], $data['reason'] ?? null),
        };

        return response()->json(['message' => 'Action sur le rapport effectuée avec succès.', 'data' => $result]);
    }
}

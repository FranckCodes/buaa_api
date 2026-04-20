<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function adminOverview(DashboardService $dashboardService): JsonResponse
    {
        return $this->successResponse(
            $dashboardService->getAdminOverview(),
            "Vue d'ensemble admin récupérée avec succès."
        );
    }

    public function adminTrends(Request $request, DashboardService $dashboardService): JsonResponse
    {
        $months = max(1, min(24, (int) $request->input('months', 6)));

        return $this->successResponse(
            $dashboardService->getAdminMonthlyTrends($months),
            'Tendances admin récupérées avec succès.'
        );
    }

    public function adminKpis(DashboardService $dashboardService): JsonResponse
    {
        return $this->successResponse(
            $dashboardService->getAdminKpis(),
            'KPIs admin récupérés avec succès.'
        );
    }

    public function adminRecentActivity(DashboardService $dashboardService): JsonResponse
    {
        return $this->successResponse(
            $dashboardService->getAdminRecentActivity(),
            'Activité récente récupérée avec succès.'
        );
    }

    public function supervisorOverview(Request $request, DashboardService $dashboardService): JsonResponse
    {
        return $this->successResponse(
            $dashboardService->getSupervisorOverview($request->user()->id),
            "Vue d'ensemble superviseur récupérée avec succès."
        );
    }

    public function clientOverview(Request $request, DashboardService $dashboardService): JsonResponse
    {
        return $this->successResponse(
            $dashboardService->getClientOverview($request->user()->id),
            "Vue d'ensemble client récupérée avec succès."
        );
    }
}

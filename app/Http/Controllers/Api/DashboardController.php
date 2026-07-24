<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RiskService;
use App\Services\IncidentService;
use App\Services\CorrectiveActionService;
use App\Services\KPIService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Class DashboardController
 * 
 * API Controller untuk Dashboard.
 * @group Dashboard
 */
class DashboardController extends Controller
{
    /**
     * Constructor
     */
    public function __construct(
        private RiskService $riskService,
        private IncidentService $incidentService,
        private CorrectiveActionService $caService,
        private KPIService $kpiService
    ) {}

    /**
     * Get dashboard overview
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function overview(Request $request): JsonResponse
    {
        $companyId = $request->user()->company_id;

        $riskStats = $this->riskService->getRiskStatistics($companyId);
        $incidentStats = $this->incidentService->getIncidentStatistics($companyId);
        $caStats = $this->caService->getCAStatistics($companyId);
        $kpiStats = $this->kpiService->getDashboardStats($companyId);

        return response()->json([
            'data' => [
                'risks' => $riskStats,
                'incidents' => $incidentStats,
                'corrective_actions' => $caStats,
                'kpis' => $kpiStats,
            ],
        ]);
    }

    /**
     * Get high risks
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function highRisks(Request $request): JsonResponse
    {
        $companyId = $request->user()->company_id;
        $risks = $this->riskService->getHighRisks($companyId);

        return response()->json([
            'data' => $risks,
        ]);
    }

    /**
     * Get overdue corrective actions
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function overdueCAs(Request $request): JsonResponse
    {
        $companyId = $request->user()->company_id;
        $cas = $this->caService->getOverdueCAs($companyId);

        return response()->json([
            'data' => $cas,
        ]);
    }

    /**
     * Get recent incidents
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function recentIncidents(Request $request): JsonResponse
    {
        $companyId = $request->user()->company_id;
        $incidents = $this->incidentService->getRecentIncidents($companyId, 5);

        return response()->json([
            'data' => $incidents,
        ]);
    }
}

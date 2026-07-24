<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreKPIRequest;
use App\Http\Requests\UpdateKPIRequest;
use App\Http\Resources\KPIResource;
use App\Services\KPIService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\JsonResponse;

/**
 * Class KPIController
 * 
 * API Controller untuk KPI Management.
 * @group KPI Management
 */
class KPIController extends Controller
{
    /**
     * Constructor
     */
    public function __construct(
        private KPIService $kpiService
    ) {}

    /**
     * Get all KPIs
     * 
     * @return ResourceCollection
     */
    public function index(Request $request): ResourceCollection
    {
        $companyId = $request->user()->company_id;
        $kpis = $this->kpiService->getAllKPIs($companyId);

        return KPIResource::collection($kpis);
    }

    /**
     * Store new KPI
     * 
     * @param StoreKPIRequest $request
     * @return JsonResponse
     */
    public function store(StoreKPIRequest $request): JsonResponse
    {
        $data = $request->validated();
        $kpi = $this->kpiService->createKPI($data);

        return response()->json([
            'message' => 'KPI created successfully',
            'data' => new KPIResource($kpi),
        ], 201);
    }

    /**
     * Get KPI by ID
     * 
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $kpi = $this->kpiService->getKPIById($id);

        if (!$kpi) {
            return response()->json([
                'message' => 'KPI not found',
            ], 404);
        }

        return response()->json([
            'data' => new KPIResource($kpi),
        ]);
    }

    /**
     * Update KPI
     * 
     * @param UpdateKPIRequest $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(UpdateKPIRequest $request, string $id): JsonResponse
    {
        try {
            $data = $request->validated();
            $kpi = $this->kpiService->updateKPI($id, $data);

            return response()->json([
                'message' => 'KPI updated successfully',
                'data' => new KPIResource($kpi),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update KPI',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Delete KPI
     * 
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $this->kpiService->deleteKPI($id);

            return response()->json([
                'message' => 'KPI deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete KPI',
                'error' => $e->getMessage(),
            ], 400);
        }
    }
}

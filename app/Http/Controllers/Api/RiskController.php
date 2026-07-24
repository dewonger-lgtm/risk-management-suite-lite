<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRiskRequest;
use App\Http\Requests\UpdateRiskRequest;
use App\Http\Resources\RiskResource;
use App\Services\RiskService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\JsonResponse;

/**
 * Class RiskController
 * 
 * API Controller untuk Risk Management.
 * @group Risk Management
 */
class RiskController extends Controller
{
    /**
     * Constructor
     */
    public function __construct(
        private RiskService $riskService
    ) {}

    /**
     * Get all risks
     * 
     * @return ResourceCollection
     */
    public function index(Request $request): ResourceCollection
    {
        $companyId = $request->user()->company_id;
        $risks = $this->riskService->getAllRisks($companyId);

        return RiskResource::collection($risks);
    }

    /**
     * Store new risk
     * 
     * @param StoreRiskRequest $request
     * @return JsonResponse
     */
    public function store(StoreRiskRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['creator_id'] = auth()->id();

        $risk = $this->riskService->createRisk($data);

        return response()->json([
            'message' => 'Risk created successfully',
            'data' => new RiskResource($risk),
        ], 201);
    }

    /**
     * Get risk by ID
     * 
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $risk = $this->riskService->getRiskById($id);

        if (!$risk) {
            return response()->json([
                'message' => 'Risk not found',
            ], 404);
        }

        return response()->json([
            'data' => new RiskResource($risk),
        ]);
    }

    /**
     * Update risk
     * 
     * @param UpdateRiskRequest $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(UpdateRiskRequest $request, string $id): JsonResponse
    {
        try {
            $data = $request->validated();
            $risk = $this->riskService->updateRisk($id, $data);

            return response()->json([
                'message' => 'Risk updated successfully',
                'data' => new RiskResource($risk),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update risk',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Delete risk
     * 
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $this->riskService->deleteRisk($id);

            return response()->json([
                'message' => 'Risk deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete risk',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get high risks
     * 
     * @param Request $request
     * @return ResourceCollection
     */
    public function highRisks(Request $request): ResourceCollection
    {
        $companyId = $request->user()->company_id;
        $risks = $this->riskService->getHighRisks($companyId);

        return RiskResource::collection($risks);
    }
}

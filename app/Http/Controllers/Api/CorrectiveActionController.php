<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCorrectiveActionRequest;
use App\Http\Requests\UpdateCorrectiveActionRequest;
use App\Http\Resources\CorrectiveActionResource;
use App\Services\CorrectiveActionService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\JsonResponse;

/**
 * Class CorrectiveActionController
 * 
 * API Controller untuk Corrective Action Management.
 * @group Corrective Action Management
 */
class CorrectiveActionController extends Controller
{
    /**
     * Constructor
     */
    public function __construct(
        private CorrectiveActionService $caService
    ) {}

    /**
     * Get all corrective actions
     * 
     * @return ResourceCollection
     */
    public function index(Request $request): ResourceCollection
    {
        $companyId = $request->user()->company_id;
        $cas = $this->caService->getAllCAs($companyId);

        return CorrectiveActionResource::collection($cas);
    }

    /**
     * Store new corrective action
     * 
     * @param StoreCorrectiveActionRequest $request
     * @return JsonResponse
     */
    public function store(StoreCorrectiveActionRequest $request): JsonResponse
    {
        $data = $request->validated();
        $ca = $this->caService->createCA($data);

        return response()->json([
            'message' => 'Corrective Action created successfully',
            'data' => new CorrectiveActionResource($ca),
        ], 201);
    }

    /**
     * Get corrective action by ID
     * 
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $ca = $this->caService->getCAById($id);

        if (!$ca) {
            return response()->json([
                'message' => 'Corrective Action not found',
            ], 404);
        }

        return response()->json([
            'data' => new CorrectiveActionResource($ca),
        ]);
    }

    /**
     * Update corrective action
     * 
     * @param UpdateCorrectiveActionRequest $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(UpdateCorrectiveActionRequest $request, string $id): JsonResponse
    {
        try {
            $data = $request->validated();
            $ca = $this->caService->updateCA($id, $data);

            return response()->json([
                'message' => 'Corrective Action updated successfully',
                'data' => new CorrectiveActionResource($ca),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update corrective action',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Delete corrective action
     * 
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $this->caService->deleteCA($id);

            return response()->json([
                'message' => 'Corrective Action deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete corrective action',
                'error' => $e->getMessage(),
            ], 400);
        }
    }
}

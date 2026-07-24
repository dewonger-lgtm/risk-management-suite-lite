<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreIncidentRequest;
use App\Http\Requests\UpdateIncidentRequest;
use App\Http\Resources\IncidentResource;
use App\Services\IncidentService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\JsonResponse;

/**
 * Class IncidentController
 * 
 * API Controller untuk Incident Management.
 * @group Incident Management
 */
class IncidentController extends Controller
{
    /**
     * Constructor
     */
    public function __construct(
        private IncidentService $incidentService
    ) {}

    /**
     * Get all incidents
     * 
     * @return ResourceCollection
     */
    public function index(Request $request): ResourceCollection
    {
        $companyId = $request->user()->company_id;
        $incidents = $this->incidentService->getAllIncidents($companyId);

        return IncidentResource::collection($incidents);
    }

    /**
     * Store new incident
     * 
     * @param StoreIncidentRequest $request
     * @return JsonResponse
     */
    public function store(StoreIncidentRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['reported_by'] = auth()->id();

        $incident = $this->incidentService->createIncident($data);

        return response()->json([
            'message' => 'Incident created successfully',
            'data' => new IncidentResource($incident),
        ], 201);
    }

    /**
     * Get incident by ID
     * 
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $incident = $this->incidentService->getIncidentById($id);

        if (!$incident) {
            return response()->json([
                'message' => 'Incident not found',
            ], 404);
        }

        return response()->json([
            'data' => new IncidentResource($incident),
        ]);
    }

    /**
     * Update incident
     * 
     * @param UpdateIncidentRequest $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(UpdateIncidentRequest $request, string $id): JsonResponse
    {
        try {
            $data = $request->validated();
            $incident = $this->incidentService->updateIncident($id, $data);

            return response()->json([
                'message' => 'Incident updated successfully',
                'data' => new IncidentResource($incident),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update incident',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Delete incident
     * 
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $this->incidentService->deleteIncident($id);

            return response()->json([
                'message' => 'Incident deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete incident',
                'error' => $e->getMessage(),
            ], 400);
        }
    }
}

<?php

namespace App\Services;

use App\Models\Incident;
use App\Repositories\Contracts\IncidentRepositoryContract;
use Illuminate\Support\Collection;

/**
 * Class IncidentService
 * 
 * Service layer untuk business logic Incident Management.
 */
class IncidentService
{
    /**
     * Constructor
     */
    public function __construct(
        private IncidentRepositoryContract $incidentRepository,
        private ActivityLogService $activityLogService
    ) {}

    /**
     * Get all incidents for a company
     */
    public function getAllIncidents(string $companyId): Collection
    {
        return $this->incidentRepository->getByCompany($companyId);
    }

    /**
     * Get incident by ID
     */
    public function getIncidentById(string $id): ?Incident
    {
        return $this->incidentRepository->getById($id);
    }

    /**
     * Get critical incidents
     */
    public function getCriticalIncidents(string $companyId): Collection
    {
        return $this->incidentRepository->getCritical($companyId);
    }

    /**
     * Get open incidents
     */
    public function getOpenIncidents(string $companyId): Collection
    {
        return $this->incidentRepository->getOpen($companyId);
    }

    /**
     * Get incident statistics
     */
    public function getIncidentStatistics(string $companyId): array
    {
        $incidents = $this->getAllIncidents($companyId);

        return [
            'total' => $incidents->count(),
            'open' => $incidents->where('status', Incident::STATUS_OPEN)->count(),
            'investigating' => $incidents->where('status', Incident::STATUS_INVESTIGATING)->count(),
            'resolved' => $incidents->where('status', Incident::STATUS_RESOLVED)->count(),
            'closed' => $incidents->where('status', Incident::STATUS_CLOSED)->count(),
            'critical' => $incidents->where('severity', Incident::SEVERITY_CRITICAL)->count(),
            'high' => $incidents->where('severity', Incident::SEVERITY_HIGH)->count(),
        ];
    }

    /**
     * Create new incident
     */
    public function createIncident(array $data): Incident
    {
        // Ensure reported_by is current user if not provided
        if (!isset($data['reported_by'])) {
            $data['reported_by'] = auth()->id();
        }

        $incident = $this->incidentRepository->store($data);

        // Log activity
        $this->activityLogService->log(
            auth()->id(),
            $data['company_id'],
            'create',
            Incident::class,
            $incident->id
        );

        return $incident;
    }

    /**
     * Update incident
     */
    public function updateIncident(string $id, array $data): Incident
    {
        $incident = $this->getIncidentById($id);

        if (!$incident) {
            throw new \Exception('Incident not found');
        }

        $oldData = $incident->toArray();

        $updatedIncident = $this->incidentRepository->update($id, $data);

        $this->activityLogService->logWithChanges(
            auth()->id(),
            $incident->company_id,
            'update',
            Incident::class,
            $id,
            $oldData,
            $updatedIncident->toArray()
        );

        return $updatedIncident;
    }

    /**
     * Assign incident for investigation
     */
    public function assignInvestigator(string $incidentId, string $investigatorId): Incident
    {
        return $this->updateIncident($incidentId, [
            'investigated_by' => $investigatorId,
            'status' => Incident::STATUS_INVESTIGATING,
        ]);
    }

    /**
     * Complete investigation
     */
    public function completeInvestigation(string $incidentId, array $findings): Incident
    {
        return $this->updateIncident($incidentId, [
            'investigation_findings' => $findings['findings'] ?? null,
            'root_cause' => $findings['root_cause'] ?? null,
            'status' => Incident::STATUS_RESOLVED,
        ]);
    }

    /**
     * Close incident
     */
    public function closeIncident(string $incidentId): Incident
    {
        return $this->updateIncident($incidentId, [
            'status' => Incident::STATUS_CLOSED,
        ]);
    }

    /**
     * Delete incident
     */
    public function deleteIncident(string $id): bool
    {
        $incident = $this->getIncidentById($id);

        if (!$incident) {
            throw new \Exception('Incident not found');
        }

        $deleted = $this->incidentRepository->delete($id);

        if ($deleted) {
            $this->activityLogService->log(
                auth()->id(),
                $incident->company_id,
                'delete',
                Incident::class,
                $id
            );
        }

        return $deleted;
    }
}

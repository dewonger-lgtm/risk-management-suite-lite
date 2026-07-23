<?php

namespace App\Services;

use App\Models\CorrectiveAction;
use App\Repositories\Contracts\CorrectiveActionRepositoryContract;
use Illuminate\Support\Collection;

/**
 * Class CorrectiveActionService
 * 
 * Service layer untuk business logic Corrective Action Management.
 */
class CorrectiveActionService
{
    /**
     * Constructor
     */
    public function __construct(
        private CorrectiveActionRepositoryContract $caRepository,
        private ActivityLogService $activityLogService,
        private ApprovalService $approvalService
    ) {}

    /**
     * Get all CAs for a company
     */
    public function getAllCAs(string $companyId): Collection
    {
        return $this->caRepository->getByCompany($companyId);
    }

    /**
     * Get CA by ID
     */
    public function getCAById(string $id): ?CorrectiveAction
    {
        return $this->caRepository->getById($id);
    }

    /**
     * Get overdue CAs
     */
    public function getOverdueCAs(string $companyId): Collection
    {
        return $this->caRepository->getOverdue($companyId);
    }

    /**
     * Get CA statistics
     */
    public function getCAStatistics(string $companyId): array
    {
        $cas = $this->getAllCAs($companyId);

        return [
            'total' => $cas->count(),
            'open' => $cas->where('status', CorrectiveAction::STATUS_OPEN)->count(),
            'in_progress' => $cas->where('status', CorrectiveAction::STATUS_IN_PROGRESS)->count(),
            'completed' => $cas->where('status', CorrectiveAction::STATUS_COMPLETED)->count(),
            'verified' => $cas->where('status', CorrectiveAction::STATUS_VERIFIED)->count(),
            'closed' => $cas->where('status', CorrectiveAction::STATUS_CLOSED)->count(),
            'overdue' => $this->getOverdueCAs($companyId)->count(),
        ];
    }

    /**
     * Create new CA
     */
    public function createCA(array $data): CorrectiveAction
    {
        $ca = $this->caRepository->store($data);

        // Log activity
        $this->activityLogService->log(
            auth()->id(),
            $data['company_id'],
            'create',
            CorrectiveAction::class,
            $ca->id
        );

        return $ca;
    }

    /**
     * Update CA
     */
    public function updateCA(string $id, array $data): CorrectiveAction
    {
        $ca = $this->getCAById($id);

        if (!$ca) {
            throw new \Exception('Corrective Action not found');
        }

        $oldData = $ca->toArray();

        $updatedCA = $this->caRepository->update($id, $data);

        $this->activityLogService->logWithChanges(
            auth()->id(),
            $ca->company_id,
            'update',
            CorrectiveAction::class,
            $id,
            $oldData,
            $updatedCA->toArray()
        );

        return $updatedCA;
    }

    /**
     * Start implementation
     */
    public function startImplementation(string $caId): CorrectiveAction
    {
        return $this->updateCA($caId, [
            'status' => CorrectiveAction::STATUS_IN_PROGRESS,
        ]);
    }

    /**
     * Complete implementation
     */
    public function completeImplementation(string $caId): CorrectiveAction
    {
        return $this->updateCA($caId, [
            'status' => CorrectiveAction::STATUS_COMPLETED,
            'implementation_date' => now()->toDateString(),
        ]);
    }

    /**
     * Verify CA effectiveness
     */
    public function verifyCA(string $caId, string $notes): CorrectiveAction
    {
        return $this->updateCA($caId, [
            'status' => CorrectiveAction::STATUS_VERIFIED,
            'verified_by' => auth()->id(),
            'verification_date' => now()->toDateString(),
            'effectiveness_notes' => $notes,
        ]);
    }

    /**
     * Close CA
     */
    public function closeCA(string $caId): CorrectiveAction
    {
        return $this->updateCA($caId, [
            'status' => CorrectiveAction::STATUS_CLOSED,
        ]);
    }

    /**
     * Delete CA
     */
    public function deleteCA(string $id): bool
    {
        $ca = $this->getCAById($id);

        if (!$ca) {
            throw new \Exception('Corrective Action not found');
        }

        $deleted = $this->caRepository->delete($id);

        if ($deleted) {
            $this->activityLogService->log(
                auth()->id(),
                $ca->company_id,
                'delete',
                CorrectiveAction::class,
                $id
            );
        }

        return $deleted;
    }

    /**
     * Get effectiveness statistics
     */
    public function getEffectivenessStats(string $companyId): array
    {
        $verifiedCAs = $this->getAllCAs($companyId)
            ->where('status', CorrectiveAction::STATUS_VERIFIED);

        $effective = $verifiedCAs->filter(function ($ca) {
            return strpos(strtolower($ca->effectiveness_notes), 'effective') !== false;
        })->count();

        $total = $verifiedCAs->count();

        return [
            'total_verified' => $total,
            'effective' => $effective,
            'effectiveness_rate' => $total > 0 ? round(($effective / $total) * 100, 2) : 0,
        ];
    }
}

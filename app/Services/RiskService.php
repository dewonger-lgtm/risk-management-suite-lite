<?php

namespace App\Services;

use App\Models\Risk;
use App\Repositories\Contracts\RiskRepositoryContract;
use Illuminate\Support\Collection;

/**
 * Class RiskService
 * 
 * Service layer untuk business logic Risk Management.
 * Memisahkan business logic dari controller dan repository.
 * 
 * Design Rationale:
 * - Centralized business logic
 * - Reusable across controllers dan API
 * - Easier to test
 * - Clear separation of concerns
 */
class RiskService
{
    /**
     * Constructor
     */
    public function __construct(
        private RiskRepositoryContract $riskRepository,
        private ActivityLogService $activityLogService
    ) {}

    /**
     * Get all risks for a company
     */
    public function getAllRisks(string $companyId): Collection
    {
        return $this->riskRepository->getByCompany($companyId);
    }

    /**
     * Get risk by ID with validation
     */
    public function getRiskById(string $id): ?Risk
    {
        return $this->riskRepository->getById($id);
    }

    /**
     * Get high risk items for dashboard
     */
    public function getHighRisks(string $companyId): Collection
    {
        return $this->riskRepository->getHighRisks($companyId);
    }

    /**
     * Get risk statistics for company
     */
    public function getRiskStatistics(string $companyId): array
    {
        $risks = $this->getAllRisks($companyId);

        return [
            'total' => $risks->count(),
            'open' => $risks->where('status', Risk::STATUS_OPEN)->count(),
            'mitigating' => $risks->where('status', Risk::STATUS_MITIGATING)->count(),
            'closed' => $risks->where('status', Risk::STATUS_CLOSED)->count(),
            'high_risk' => $risks->where('inherent_risk_score', '>=', 15)->count(),
            'critical' => $risks->where('inherent_risk_score', '>=', 20)->count(),
        ];
    }

    /**
     * Create new risk
     */
    public function createRisk(array $data): Risk
    {
        // Calculate inherent risk score
        $data['inherent_risk_score'] = $data['likelihood'] * $data['impact'];

        // Create risk
        $risk = $this->riskRepository->store($data);

        // Log activity
        $this->activityLogService->log(
            auth()->id(),
            $data['company_id'],
            'create',
            Risk::class,
            $risk->id
        );

        return $risk;
    }

    /**
     * Update risk
     */
    public function updateRisk(string $id, array $data): Risk
    {
        $risk = $this->getRiskById($id);

        if (!$risk) {
            throw new \Exception('Risk not found');
        }

        // Recalculate inherent risk score if likelihood or impact changed
        if (isset($data['likelihood']) || isset($data['impact'])) {
            $likelihood = $data['likelihood'] ?? $risk->likelihood;
            $impact = $data['impact'] ?? $risk->impact;
            $data['inherent_risk_score'] = $likelihood * $impact;
        }

        // Get old values for audit
        $oldData = $risk->toArray();

        // Update
        $updatedRisk = $this->riskRepository->update($id, $data);

        // Log activity with changes
        $this->activityLogService->logWithChanges(
            auth()->id(),
            $risk->company_id,
            'update',
            Risk::class,
            $id,
            $oldData,
            $updatedRisk->toArray()
        );

        return $updatedRisk;
    }

    /**
     * Delete risk
     */
    public function deleteRisk(string $id): bool
    {
        $risk = $this->getRiskById($id);

        if (!$risk) {
            throw new \Exception('Risk not found');
        }

        $deleted = $this->riskRepository->delete($id);

        if ($deleted) {
            $this->activityLogService->log(
                auth()->id(),
                $risk->company_id,
                'delete',
                Risk::class,
                $id
            );
        }

        return $deleted;
    }

    /**
     * Get risks by status
     */
    public function getRisksByStatus(string $companyId, string $status): Collection
    {
        return $this->riskRepository->getByStatus($companyId, $status);
    }

    /**
     * Get mitigation effectiveness
     */
    public function getMitigationEffectiveness(string $companyId): array
    {
        $risks = $this->getAllRisks($companyId)->where('residual_risk_score', '!=', null);

        if ($risks->isEmpty()) {
            return ['average' => 0, 'total' => 0];
        }

        $totalEffectiveness = 0;
        $count = 0;

        foreach ($risks as $risk) {
            if ($risk->inherent_risk_score > 0) {
                $effectiveness = (($risk->inherent_risk_score - $risk->residual_risk_score) / $risk->inherent_risk_score) * 100;
                $totalEffectiveness += $effectiveness;
                $count++;
            }
        }

        return [
            'average' => $count > 0 ? round($totalEffectiveness / $count, 2) : 0,
            'total' => $count,
        ];
    }
}

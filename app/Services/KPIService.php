<?php

namespace App\Services;

use App\Models\KPI;
use App\Models\KPIActual;
use App\Models\KPITarget;
use App\Repositories\Contracts\KPIRepositoryContract;
use Illuminate\Support\Collection;

/**
 * Class KPIService
 * 
 * Service layer untuk business logic KPI Management.
 */
class KPIService
{
    /**
     * Constructor
     */
    public function __construct(
        private KPIRepositoryContract $kpiRepository,
        private ActivityLogService $activityLogService
    ) {}

    /**
     * Get all KPIs for a company
     */
    public function getAllKPIs(string $companyId): Collection
    {
        return $this->kpiRepository->getByCompany($companyId);
    }

    /**
     * Get KPI by ID
     */
    public function getKPIById(string $id): ?KPI
    {
        return $this->kpiRepository->getById($id);
    }

    /**
     * Get active KPIs
     */
    public function getActiveKPIs(string $companyId): Collection
    {
        return $this->kpiRepository->getActive($companyId);
    }

    /**
     * Create new KPI
     */
    public function createKPI(array $data): KPI
    {
        $kpi = $this->kpiRepository->store($data);

        $this->activityLogService->log(
            auth()->id(),
            $data['company_id'],
            'create',
            KPI::class,
            $kpi->id
        );

        return $kpi;
    }

    /**
     * Update KPI
     */
    public function updateKPI(string $id, array $data): KPI
    {
        $kpi = $this->getKPIById($id);

        if (!$kpi) {
            throw new \Exception('KPI not found');
        }

        $oldData = $kpi->toArray();

        $updatedKPI = $this->kpiRepository->update($id, $data);

        $this->activityLogService->logWithChanges(
            auth()->id(),
            $kpi->company_id,
            'update',
            KPI::class,
            $id,
            $oldData,
            $updatedKPI->toArray()
        );

        return $updatedKPI;
    }

    /**
     * Delete KPI
     */
    public function deleteKPI(string $id): bool
    {
        $kpi = $this->getKPIById($id);

        if (!$kpi) {
            throw new \Exception('KPI not found');
        }

        $deleted = $this->kpiRepository->delete($id);

        if ($deleted) {
            $this->activityLogService->log(
                auth()->id(),
                $kpi->company_id,
                'delete',
                KPI::class,
                $id
            );
        }

        return $deleted;
    }

    /**
     * Set target for period
     */
    public function setTarget(string $kpiId, string $period, float $targetValue): KPITarget
    {
        // Check if target already exists
        $target = KPITarget::where('kpi_id', $kpiId)
            ->where('period', $period)
            ->first();

        if ($target) {
            $target->update(['target_value' => $targetValue]);
            return $target;
        }

        return KPITarget::create([
            'kpi_id' => $kpiId,
            'period' => $period,
            'target_value' => $targetValue,
        ]);
    }

    /**
     * Record actual value
     */
    public function recordActual(string $kpiId, string $period, float $actualValue): KPIActual
    {
        // Check if actual already exists
        $actual = KPIActual::where('kpi_id', $kpiId)
            ->where('period', $period)
            ->first();

        if ($actual) {
            $actual->update([
                'actual_value' => $actualValue,
                'recorded_date' => now(),
            ]);
            return $actual;
        }

        return KPIActual::create([
            'kpi_id' => $kpiId,
            'period' => $period,
            'actual_value' => $actualValue,
            'recorded_by' => auth()->id(),
            'recorded_date' => now(),
        ]);
    }

    /**
     * Get KPI performance for period
     */
    public function getPerformance(string $kpiId, string $period): array
    {
        $kpi = $this->getKPIById($kpiId);
        $target = KPITarget::where('kpi_id', $kpiId)
            ->where('period', $period)
            ->first();
        $actual = KPIActual::where('kpi_id', $kpiId)
            ->where('period', $period)
            ->first();

        $targetValue = $target?->target_value ?? $kpi->target_value;
        $actualValue = $actual?->actual_value ?? 0;

        $variance = $actualValue - $targetValue;
        $variancePercent = $targetValue > 0 ? round(($variance / $targetValue) * 100, 2) : 0;
        $achievement = $targetValue > 0 ? round(($actualValue / $targetValue) * 100, 2) : 0;

        return [
            'target' => $targetValue,
            'actual' => $actualValue,
            'variance' => $variance,
            'variance_percent' => $variancePercent,
            'achievement_percent' => $achievement,
            'status' => $achievement >= 100 ? 'on_target' : ($achievement >= 80 ? 'warning' : 'below_target'),
        ];
    }

    /**
     * Get dashboard statistics
     */
    public function getDashboardStats(string $companyId): array
    {
        $kpis = $this->getActiveKPIs($companyId);

        $onTarget = 0;
        $warning = 0;
        $belowTarget = 0;

        $currentPeriod = now()->format('Y-m');

        foreach ($kpis as $kpi) {
            $performance = $this->getPerformance($kpi->id, $currentPeriod);
            match ($performance['status']) {
                'on_target' => $onTarget++,
                'warning' => $warning++,
                'below_target' => $belowTarget++,
            };
        }

        return [
            'total' => $kpis->count(),
            'on_target' => $onTarget,
            'warning' => $warning,
            'below_target' => $belowTarget,
        ];
    }
}

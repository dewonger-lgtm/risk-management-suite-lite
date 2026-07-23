<?php

namespace App\Repositories\Eloquent;

use App\Models\KPI;
use App\Repositories\Contracts\KPIRepositoryContract;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

/**
 * Class KPIRepository
 * 
 * Implementasi Repository Pattern untuk KPI menggunakan Eloquent ORM.
 */
class KPIRepository implements KPIRepositoryContract
{
    /**
     * Get all KPIs by company
     */
    public function getByCompany(string $companyId): Collection
    {
        return KPI::where('company_id', $companyId)
            ->with(['owner', 'department', 'targets', 'actuals'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get KPI by ID
     */
    public function getById(string $id): ?KPI
    {
        return KPI::with(['owner', 'department', 'targets', 'actuals'])
            ->find($id);
    }

    /**
     * Get KPI by code
     */
    public function getByCode(string $code): ?KPI
    {
        return KPI::where('code', $code)
            ->with(['owner', 'department'])
            ->first();
    }

    /**
     * Get all KPIs with pagination
     */
    public function paginate(string $companyId, int $perPage = 15): Paginator
    {
        return KPI::where('company_id', $companyId)
            ->with(['owner', 'department'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get active KPIs
     */
    public function getActive(string $companyId): Collection
    {
        return KPI::where('company_id', $companyId)
            ->where('status', KPI::STATUS_ACTIVE)
            ->with(['owner', 'department'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get KPIs by department
     */
    public function getByDepartment(string $departmentId): Collection
    {
        return KPI::where('department_id', $departmentId)
            ->where('status', KPI::STATUS_ACTIVE)
            ->with(['owner', 'targets', 'actuals'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get KPIs by owner
     */
    public function getByOwner(string $ownerId): Collection
    {
        return KPI::where('owner_id', $ownerId)
            ->with(['department', 'targets', 'actuals'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get KPIs by frequency
     */
    public function getByFrequency(string $companyId, string $frequency): Collection
    {
        return KPI::where('company_id', $companyId)
            ->where('frequency', $frequency)
            ->where('status', KPI::STATUS_ACTIVE)
            ->with(['owner'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Create new KPI
     */
    public function store(array $data): KPI
    {
        return KPI::create($data);
    }

    /**
     * Update existing KPI
     */
    public function update(string $id, array $data): KPI
    {
        $kpi = $this->getById($id);
        $kpi->update($data);
        return $kpi->fresh(['owner', 'department']);
    }

    /**
     * Delete KPI (soft delete)
     */
    public function delete(string $id): bool
    {
        $kpi = KPI::find($id);
        return $kpi ? $kpi->delete() : false;
    }
}

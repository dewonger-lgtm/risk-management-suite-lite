<?php

namespace App\Repositories\Contracts;

use App\Models\KPI;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

/**
 * Interface KPIRepositoryContract
 * 
 * Defines contract untuk KPI Repository.
 */
interface KPIRepositoryContract
{
    /**
     * Get all KPIs by company
     */
    public function getByCompany(string $companyId): Collection;

    /**
     * Get KPI by ID
     */
    public function getById(string $id): ?KPI;

    /**
     * Get KPI by code
     */
    public function getByCode(string $code): ?KPI;

    /**
     * Get all KPIs with pagination
     */
    public function paginate(string $companyId, int $perPage = 15): Paginator;

    /**
     * Get active KPIs
     */
    public function getActive(string $companyId): Collection;

    /**
     * Get KPIs by department
     */
    public function getByDepartment(string $departmentId): Collection;

    /**
     * Get KPIs by owner
     */
    public function getByOwner(string $ownerId): Collection;

    /**
     * Get KPIs by frequency
     */
    public function getByFrequency(string $companyId, string $frequency): Collection;

    /**
     * Create new KPI
     */
    public function store(array $data): KPI;

    /**
     * Update existing KPI
     */
    public function update(string $id, array $data): KPI;

    /**
     * Delete KPI (soft delete)
     */
    public function delete(string $id): bool;
}

<?php

namespace App\Repositories\Eloquent;

use App\Models\Risk;
use App\Repositories\Contracts\RiskRepositoryContract;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

/**
 * Class RiskRepository
 * 
 * Implementasi Repository Pattern untuk Risk menggunakan Eloquent ORM.
 * 
 * Design Rationale:
 * - Mengabstraksi Eloquent queries di repository
 * - Memudahkan testing dengan mocking
 * - Centralized data access logic
 */
class RiskRepository implements RiskRepositoryContract
{
    /**
     * Get all risks by company
     */
    public function getByCompany(string $companyId): Collection
    {
        return Risk::where('company_id', $companyId)
            ->with(['category', 'owner', 'creator'])
            ->get();
    }

    /**
     * Get risk by ID
     */
    public function getById(string $id): ?Risk
    {
        return Risk::with(['category', 'owner', 'creator', 'correctiveActions'])
            ->find($id);
    }

    /**
     * Get risk by code
     */
    public function getByCode(string $code): ?Risk
    {
        return Risk::where('code', $code)
            ->with(['category', 'owner', 'creator'])
            ->first();
    }

    /**
     * Get all risks with pagination
     */
    public function paginate(string $companyId, int $perPage = 15): Paginator
    {
        return Risk::where('company_id', $companyId)
            ->with(['category', 'owner', 'creator'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get high risk items (score >= 15)
     */
    public function getHighRisks(string $companyId): Collection
    {
        return Risk::where('company_id', $companyId)
            ->where('inherent_risk_score', '>=', 15)
            ->with(['category', 'owner'])
            ->orderBy('inherent_risk_score', 'desc')
            ->get();
    }

    /**
     * Get risks by status
     */
    public function getByStatus(string $companyId, string $status): Collection
    {
        return Risk::where('company_id', $companyId)
            ->where('status', $status)
            ->with(['category', 'owner'])
            ->get();
    }

    /**
     * Get risks by owner
     */
    public function getByOwner(string $ownerId): Collection
    {
        return Risk::where('owner_id', $ownerId)
            ->with(['company', 'category', 'creator'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get risks by category
     */
    public function getByCategory(string $categoryId): Collection
    {
        return Risk::where('category_id', $categoryId)
            ->with(['owner', 'creator'])
            ->get();
    }

    /**
     * Create new risk
     */
    public function store(array $data): Risk
    {
        return Risk::create($data);
    }

    /**
     * Update existing risk
     */
    public function update(string $id, array $data): Risk
    {
        $risk = $this->getById($id);
        $risk->update($data);
        return $risk->fresh(['category', 'owner', 'creator']);
    }

    /**
     * Delete risk (soft delete)
     */
    public function delete(string $id): bool
    {
        $risk = Risk::find($id);
        return $risk ? $risk->delete() : false;
    }

    /**
     * Restore soft-deleted risk
     */
    public function restore(string $id): bool
    {
        $risk = Risk::onlyTrashed()->find($id);
        return $risk ? $risk->restore() : false;
    }

    /**
     * Force delete risk
     */
    public function forceDelete(string $id): bool
    {
        $risk = Risk::withTrashed()->find($id);
        return $risk ? $risk->forceDelete() : false;
    }

    /**
     * Get risks by date range
     */
    public function getByDateRange(string $companyId, string $startDate, string $endDate): Collection
    {
        return Risk::where('company_id', $companyId)
            ->whereBetween('risk_date', [$startDate, $endDate])
            ->with(['category', 'owner'])
            ->orderBy('risk_date', 'desc')
            ->get();
    }
}

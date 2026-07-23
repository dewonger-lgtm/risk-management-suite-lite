<?php

namespace App\Repositories\Contracts;

use App\Models\Risk;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

/**
 * Interface RiskRepositoryContract
 * 
 * Defines contract untuk Risk Repository.
 * Semua implementasi repository harus mengikuti interface ini.
 * 
 * Design Rationale:
 * - Memisahkan business logic dari database implementation
 * - Memudahkan testing dengan mock repository
 * - Memungkinkan switching database tanpa mengubah business logic
 */
interface RiskRepositoryContract
{
    /**
     * Get all risks by company
     */
    public function getByCompany(string $companyId): Collection;

    /**
     * Get risk by ID
     */
    public function getById(string $id): ?Risk;

    /**
     * Get risk by code
     */
    public function getByCode(string $code): ?Risk;

    /**
     * Get all risks with pagination
     */
    public function paginate(string $companyId, int $perPage = 15): Paginator;

    /**
     * Get high risk items (score >= 15)
     */
    public function getHighRisks(string $companyId): Collection;

    /**
     * Get risks by status
     */
    public function getByStatus(string $companyId, string $status): Collection;

    /**
     * Get risks by owner
     */
    public function getByOwner(string $ownerId): Collection;

    /**
     * Get risks by category
     */
    public function getByCategory(string $categoryId): Collection;

    /**
     * Create new risk
     */
    public function store(array $data): Risk;

    /**
     * Update existing risk
     */
    public function update(string $id, array $data): Risk;

    /**
     * Delete risk (soft delete)
     */
    public function delete(string $id): bool;

    /**
     * Restore soft-deleted risk
     */
    public function restore(string $id): bool;

    /**
     * Force delete risk
     */
    public function forceDelete(string $id): bool;

    /**
     * Get risks by date range
     */
    public function getByDateRange(string $companyId, string $startDate, string $endDate): Collection;
}

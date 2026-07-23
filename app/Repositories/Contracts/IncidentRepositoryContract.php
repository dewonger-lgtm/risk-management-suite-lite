<?php

namespace App\Repositories\Contracts;

use App\Models\Incident;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

/**
 * Interface IncidentRepositoryContract
 * 
 * Defines contract untuk Incident Repository.
 */
interface IncidentRepositoryContract
{
    /**
     * Get all incidents by company
     */
    public function getByCompany(string $companyId): Collection;

    /**
     * Get incident by ID
     */
    public function getById(string $id): ?Incident;

    /**
     * Get incident by code
     */
    public function getByCode(string $code): ?Incident;

    /**
     * Get all incidents with pagination
     */
    public function paginate(string $companyId, int $perPage = 15): Paginator;

    /**
     * Get critical incidents
     */
    public function getCritical(string $companyId): Collection;

    /**
     * Get open incidents (not closed)
     */
    public function getOpen(string $companyId): Collection;

    /**
     * Get incidents by status
     */
    public function getByStatus(string $companyId, string $status): Collection;

    /**
     * Get incidents by severity
     */
    public function getBySeverity(string $companyId, string $severity): Collection;

    /**
     * Get incidents by type
     */
    public function getByType(string $companyId, string $type): Collection;

    /**
     * Get incidents reported by user
     */
    public function getByReporter(string $reporterId): Collection;

    /**
     * Create new incident
     */
    public function store(array $data): Incident;

    /**
     * Update existing incident
     */
    public function update(string $id, array $data): Incident;

    /**
     * Delete incident (soft delete)
     */
    public function delete(string $id): bool;

    /**
     * Get incidents by date range
     */
    public function getByDateRange(string $companyId, string $startDate, string $endDate): Collection;
}

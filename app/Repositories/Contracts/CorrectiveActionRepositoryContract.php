<?php

namespace App\Repositories\Contracts;

use App\Models\CorrectiveAction;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

/**
 * Interface CorrectiveActionRepositoryContract
 * 
 * Defines contract untuk Corrective Action Repository.
 */
interface CorrectiveActionRepositoryContract
{
    /**
     * Get all corrective actions by company
     */
    public function getByCompany(string $companyId): Collection;

    /**
     * Get CA by ID
     */
    public function getById(string $id): ?CorrectiveAction;

    /**
     * Get CA by code
     */
    public function getByCode(string $code): ?CorrectiveAction;

    /**
     * Get all CAs with pagination
     */
    public function paginate(string $companyId, int $perPage = 15): Paginator;

    /**
     * Get overdue CAs
     */
    public function getOverdue(string $companyId): Collection;

    /**
     * Get CAs by status
     */
    public function getByStatus(string $companyId, string $status): Collection;

    /**
     * Get CAs by priority
     */
    public function getByPriority(string $companyId, string $priority): Collection;

    /**
     * Get CAs assigned to user
     */
    public function getAssignedTo(string $userId): Collection;

    /**
     * Get CAs related to incident
     */
    public function getByIncident(string $incidentId): Collection;

    /**
     * Get CAs related to risk
     */
    public function getByRisk(string $riskId): Collection;

    /**
     * Create new CA
     */
    public function store(array $data): CorrectiveAction;

    /**
     * Update existing CA
     */
    public function update(string $id, array $data): CorrectiveAction;

    /**
     * Delete CA (soft delete)
     */
    public function delete(string $id): bool;

    /**
     * Get CAs by date range
     */
    public function getByDateRange(string $companyId, string $startDate, string $endDate): Collection;
}

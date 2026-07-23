<?php

namespace App\Repositories\Eloquent;

use App\Models\CorrectiveAction;
use App\Repositories\Contracts\CorrectiveActionRepositoryContract;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

/**
 * Class CorrectiveActionRepository
 * 
 * Implementasi Repository Pattern untuk Corrective Action menggunakan Eloquent ORM.
 */
class CorrectiveActionRepository implements CorrectiveActionRepositoryContract
{
    /**
     * Get all corrective actions by company
     */
    public function getByCompany(string $companyId): Collection
    {
        return CorrectiveAction::where('company_id', $companyId)
            ->with(['assignee', 'verifier', 'incident', 'risk'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get CA by ID
     */
    public function getById(string $id): ?CorrectiveAction
    {
        return CorrectiveAction::with(['assignee', 'verifier', 'incident', 'risk', 'approvals'])
            ->find($id);
    }

    /**
     * Get CA by code
     */
    public function getByCode(string $code): ?CorrectiveAction
    {
        return CorrectiveAction::where('code', $code)
            ->with(['assignee', 'verifier'])
            ->first();
    }

    /**
     * Get all CAs with pagination
     */
    public function paginate(string $companyId, int $perPage = 15): Paginator
    {
        return CorrectiveAction::where('company_id', $companyId)
            ->with(['assignee', 'verifier'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get overdue CAs
     */
    public function getOverdue(string $companyId): Collection
    {
        return CorrectiveAction::where('company_id', $companyId)
            ->where('due_date', '<', now()->toDateString())
            ->whereNotIn('status', [
                CorrectiveAction::STATUS_COMPLETED,
                CorrectiveAction::STATUS_VERIFIED,
                CorrectiveAction::STATUS_CLOSED
            ])
            ->with(['assignee'])
            ->orderBy('due_date', 'asc')
            ->get();
    }

    /**
     * Get CAs by status
     */
    public function getByStatus(string $companyId, string $status): Collection
    {
        return CorrectiveAction::where('company_id', $companyId)
            ->where('status', $status)
            ->with(['assignee', 'verifier'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get CAs by priority
     */
    public function getByPriority(string $companyId, string $priority): Collection
    {
        return CorrectiveAction::where('company_id', $companyId)
            ->where('priority', $priority)
            ->with(['assignee'])
            ->orderBy('due_date', 'asc')
            ->get();
    }

    /**
     * Get CAs assigned to user
     */
    public function getAssignedTo(string $userId): Collection
    {
        return CorrectiveAction::where('assigned_to', $userId)
            ->with(['incident', 'risk', 'verifier'])
            ->orderBy('due_date', 'asc')
            ->get();
    }

    /**
     * Get CAs related to incident
     */
    public function getByIncident(string $incidentId): Collection
    {
        return CorrectiveAction::where('incident_id', $incidentId)
            ->with(['assignee', 'verifier'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get CAs related to risk
     */
    public function getByRisk(string $riskId): Collection
    {
        return CorrectiveAction::where('risk_id', $riskId)
            ->with(['assignee', 'verifier'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Create new CA
     */
    public function store(array $data): CorrectiveAction
    {
        return CorrectiveAction::create($data);
    }

    /**
     * Update existing CA
     */
    public function update(string $id, array $data): CorrectiveAction
    {
        $ca = $this->getById($id);
        $ca->update($data);
        return $ca->fresh(['assignee', 'verifier']);
    }

    /**
     * Delete CA (soft delete)
     */
    public function delete(string $id): bool
    {
        $ca = CorrectiveAction::find($id);
        return $ca ? $ca->delete() : false;
    }

    /**
     * Get CAs by date range
     */
    public function getByDateRange(string $companyId, string $startDate, string $endDate): Collection
    {
        return CorrectiveAction::where('company_id', $companyId)
            ->whereBetween('due_date', [$startDate, $endDate])
            ->with(['assignee'])
            ->orderBy('due_date', 'asc')
            ->get();
    }
}

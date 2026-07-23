<?php

namespace App\Repositories\Eloquent;

use App\Models\Incident;
use App\Repositories\Contracts\IncidentRepositoryContract;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

/**
 * Class IncidentRepository
 * 
 * Implementasi Repository Pattern untuk Incident menggunakan Eloquent ORM.
 */
class IncidentRepository implements IncidentRepositoryContract
{
    /**
     * Get all incidents by company
     */
    public function getByCompany(string $companyId): Collection
    {
        return Incident::where('company_id', $companyId)
            ->with(['reporter', 'investigator', 'correctiveActions'])
            ->orderBy('reported_date', 'desc')
            ->get();
    }

    /**
     * Get incident by ID
     */
    public function getById(string $id): ?Incident
    {
        return Incident::with(['reporter', 'investigator', 'correctiveActions'])
            ->find($id);
    }

    /**
     * Get incident by code
     */
    public function getByCode(string $code): ?Incident
    {
        return Incident::where('code', $code)
            ->with(['reporter', 'investigator'])
            ->first();
    }

    /**
     * Get all incidents with pagination
     */
    public function paginate(string $companyId, int $perPage = 15): Paginator
    {
        return Incident::where('company_id', $companyId)
            ->with(['reporter', 'investigator'])
            ->orderBy('reported_date', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get critical incidents
     */
    public function getCritical(string $companyId): Collection
    {
        return Incident::where('company_id', $companyId)
            ->where('severity', Incident::SEVERITY_CRITICAL)
            ->with(['reporter'])
            ->orderBy('reported_date', 'desc')
            ->get();
    }

    /**
     * Get open incidents (not closed)
     */
    public function getOpen(string $companyId): Collection
    {
        return Incident::where('company_id', $companyId)
            ->whereIn('status', [Incident::STATUS_OPEN, Incident::STATUS_INVESTIGATING])
            ->with(['reporter'])
            ->orderBy('reported_date', 'desc')
            ->get();
    }

    /**
     * Get incidents by status
     */
    public function getByStatus(string $companyId, string $status): Collection
    {
        return Incident::where('company_id', $companyId)
            ->where('status', $status)
            ->with(['reporter', 'investigator'])
            ->orderBy('reported_date', 'desc')
            ->get();
    }

    /**
     * Get incidents by severity
     */
    public function getBySeverity(string $companyId, string $severity): Collection
    {
        return Incident::where('company_id', $companyId)
            ->where('severity', $severity)
            ->with(['reporter'])
            ->orderBy('reported_date', 'desc')
            ->get();
    }

    /**
     * Get incidents by type
     */
    public function getByType(string $companyId, string $type): Collection
    {
        return Incident::where('company_id', $companyId)
            ->where('type', $type)
            ->with(['reporter'])
            ->orderBy('reported_date', 'desc')
            ->get();
    }

    /**
     * Get incidents reported by user
     */
    public function getByReporter(string $reporterId): Collection
    {
        return Incident::where('reported_by', $reporterId)
            ->with(['investigator', 'correctiveActions'])
            ->orderBy('reported_date', 'desc')
            ->get();
    }

    /**
     * Create new incident
     */
    public function store(array $data): Incident
    {
        return Incident::create($data);
    }

    /**
     * Update existing incident
     */
    public function update(string $id, array $data): Incident
    {
        $incident = $this->getById($id);
        $incident->update($data);
        return $incident->fresh(['reporter', 'investigator']);
    }

    /**
     * Delete incident (soft delete)
     */
    public function delete(string $id): bool
    {
        $incident = Incident::find($id);
        return $incident ? $incident->delete() : false;
    }

    /**
     * Get incidents by date range
     */
    public function getByDateRange(string $companyId, string $startDate, string $endDate): Collection
    {
        return Incident::where('company_id', $companyId)
            ->whereBetween('occurred_date', [$startDate, $endDate])
            ->with(['reporter'])
            ->orderBy('occurred_date', 'desc')
            ->get();
    }
}

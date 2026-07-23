<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model Incident
 * 
 * Menyimpan pelaporan insiden dengan workflow investigation.
 * 
 * @property string $id (UUID)
 * @property string $company_id
 * @property string $reported_by
 * @property string $investigated_by
 * @property string $code
 * @property string $title
 * @property string $description
 * @property string $type
 * @property string $severity
 * @property string $status
 * @property \DateTime $reported_date
 * @property \DateTime $occurred_date
 * @property string $investigation_findings
 * @property string $root_cause
 * @property \DateTime $created_at
 * @property \DateTime $updated_at
 * @property \DateTime $deleted_at
 */
class Incident extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * Table name
     */
    protected $table = 'incidents';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'company_id',
        'reported_by',
        'investigated_by',
        'code',
        'title',
        'description',
        'type',
        'severity',
        'status',
        'reported_date',
        'occurred_date',
        'investigation_findings',
        'root_cause',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'reported_date' => 'datetime',
        'occurred_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Status Enums
     */
    const STATUS_OPEN = 'open';
    const STATUS_INVESTIGATING = 'investigating';
    const STATUS_RESOLVED = 'resolved';
    const STATUS_CLOSED = 'closed';

    /**
     * Severity Enums
     */
    const SEVERITY_CRITICAL = 'critical';
    const SEVERITY_HIGH = 'high';
    const SEVERITY_MEDIUM = 'medium';
    const SEVERITY_LOW = 'low';

    /**
     * Type Enums
     */
    const TYPE_SAFETY = 'safety';
    const TYPE_QUALITY = 'quality';
    const TYPE_SECURITY = 'security';
    const TYPE_OPERATIONAL = 'operational';

    /**
     * Relationships
     */

    /**
     * Get the company that owns this incident
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the user who reported this incident
     */
    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    /**
     * Get the user who investigated this incident
     */
    public function investigator()
    {
        return $this->belongsTo(User::class, 'investigated_by');
    }

    /**
     * Get all corrective actions for this incident
     */
    public function correctiveActions()
    {
        return $this->hasMany(CorrectiveAction::class, 'incident_id');
    }

    /**
     * Get all approvals for this incident
     */
    public function approvals()
    {
        return $this->morphMany(Approval::class, 'approvalable');
    }

    /**
     * Scopes
     */

    /**
     * Scope untuk filter by status
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope untuk filter by severity
     */
    public function scopeBySeverity($query, string $severity)
    {
        return $query->where('severity', $severity);
    }

    /**
     * Scope untuk filter by company
     */
    public function scopeByCompany($query, string $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope untuk filter critical incidents
     */
    public function scopeCritical($query)
    {
        return $query->where('severity', self::SEVERITY_CRITICAL);
    }

    /**
     * Scope untuk filter open incidents
     */
    public function scopeOpen($query)
    {
        return $query->whereIn('status', [self::STATUS_OPEN, self::STATUS_INVESTIGATING]);
    }
}

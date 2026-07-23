<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model CorrectiveAction
 * 
 * Menyimpan rencana aksi korektif/preventif yang dapat dikaitkan dengan incident atau risk.
 * 
 * @property string $id (UUID)
 * @property string $company_id
 * @property string $incident_id
 * @property string $risk_id
 * @property string $assigned_to
 * @property string $verified_by
 * @property string $code
 * @property string $title
 * @property string $description
 * @property string $type
 * @property string $priority
 * @property string $status
 * @property date $due_date
 * @property date $implementation_date
 * @property date $verification_date
 * @property string $effectiveness_notes
 * @property \DateTime $created_at
 * @property \DateTime $updated_at
 * @property \DateTime $deleted_at
 */
class CorrectiveAction extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * Table name
     */
    protected $table = 'corrective_actions';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'company_id',
        'incident_id',
        'risk_id',
        'assigned_to',
        'verified_by',
        'code',
        'title',
        'description',
        'type',
        'priority',
        'status',
        'due_date',
        'implementation_date',
        'verification_date',
        'effectiveness_notes',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'due_date' => 'date',
        'implementation_date' => 'date',
        'verification_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Status Enums
     */
    const STATUS_OPEN = 'open';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_VERIFIED = 'verified';
    const STATUS_CLOSED = 'closed';

    /**
     * Type Enums
     */
    const TYPE_CORRECTIVE = 'corrective';
    const TYPE_PREVENTIVE = 'preventive';

    /**
     * Priority Enums
     */
    const PRIORITY_CRITICAL = 'critical';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_LOW = 'low';

    /**
     * Relationships
     */

    /**
     * Get the company that owns this CA
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the incident related to this CA
     */
    public function incident()
    {
        return $this->belongsTo(Incident::class, 'incident_id');
    }

    /**
     * Get the risk related to this CA
     */
    public function risk()
    {
        return $this->belongsTo(Risk::class, 'risk_id');
    }

    /**
     * Get the user this CA is assigned to
     */
    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the user who verified this CA
     */
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Get all approvals for this CA
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
     * Scope untuk filter by priority
     */
    public function scopeByPriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope untuk filter by company
     */
    public function scopeByCompany($query, string $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope untuk filter by assigned user
     */
    public function scopeAssignedTo($query, string $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    /**
     * Scope untuk filter overdue CA
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
            ->whereNotIn('status', [self::STATUS_COMPLETED, self::STATUS_VERIFIED, self::STATUS_CLOSED]);
    }

    /**
     * Helper Methods
     */

    /**
     * Check if CA is overdue
     */
    public function isOverdue(): bool
    {
        return $this->due_date < now()->toDateString() && 
               !in_array($this->status, [self::STATUS_COMPLETED, self::STATUS_VERIFIED, self::STATUS_CLOSED]);
    }

    /**
     * Get days until due date
     */
    public function getDaysUntilDueAttribute(): ?int
    {
        if ($this->due_date === null) {
            return null;
        }

        return now()->toDateString() <= $this->due_date->toDateString() 
            ? now()->diffInDays($this->due_date) 
            : -now()->diffInDays($this->due_date);
    }
}

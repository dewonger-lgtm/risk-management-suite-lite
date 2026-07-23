<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Approval
 * 
 * Menyimpan workflow approval yang bersifat polymorphic.
 * Dapat digunakan untuk approval CA, Risk, atau Incident.
 * 
 * @property string $id (UUID)
 * @property string $company_id
 * @property string $approved_by
 * @property string $approvalable_type
 * @property string $approvalable_id
 * @property int $sequence
 * @property string $status
 * @property string $comments
 * @property \DateTime $approved_date
 * @property \DateTime $created_at
 * @property \DateTime $updated_at
 */
class Approval extends Model
{
    use HasUuids;

    /**
     * Table name
     */
    protected $table = 'approvals';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'company_id',
        'approved_by',
        'approvalable_type',
        'approvalable_id',
        'sequence',
        'status',
        'comments',
        'approved_date',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'approved_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Status Enums
     */
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    /**
     * Relationships
     */

    /**
     * Get the company that owns this approval
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the user who approved this
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the approvalable model (polymorphic)
     */
    public function approvalable()
    {
        return $this->morphTo();
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
     * Scope untuk filter pending approvals
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope untuk filter by company
     */
    public function scopeByCompany($query, string $companyId)
    {
        return $query->where('company_id', $companyId);
    }
}

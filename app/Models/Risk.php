<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model Risk
 * 
 * Menyimpan register risiko perusahaan dengan risk scoring calculation.
 * 
 * @property string $id (UUID)
 * @property string $company_id
 * @property string $category_id
 * @property string $owner_id
 * @property string $created_by
 * @property string $code
 * @property string $title
 * @property string $description
 * @property int $likelihood (1-5)
 * @property int $impact (1-5)
 * @property int $inherent_risk_score
 * @property string $mitigation_plan
 * @property int $residual_risk_score
 * @property string $status
 * @property \DateTime $risk_date
 * @property \DateTime $created_at
 * @property \DateTime $updated_at
 * @property \DateTime $deleted_at
 */
class Risk extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * Table name
     */
    protected $table = 'risks';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'company_id',
        'category_id',
        'owner_id',
        'created_by',
        'code',
        'title',
        'description',
        'likelihood',
        'impact',
        'inherent_risk_score',
        'mitigation_plan',
        'residual_risk_score',
        'status',
        'risk_date',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'risk_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Status Enums
     */
    const STATUS_OPEN = 'open';
    const STATUS_MITIGATING = 'mitigating';
    const STATUS_CLOSED = 'closed';

    /**
     * Relationships
     */

    /**
     * Get the company that owns this risk
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the category of this risk
     */
    public function category()
    {
        return $this->belongsTo(RiskCategory::class, 'category_id');
    }

    /**
     * Get the owner of this risk
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get who created this risk
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all corrective actions for this risk
     */
    public function correctiveActions()
    {
        return $this->hasMany(CorrectiveAction::class, 'risk_id');
    }

    /**
     * Get all approvals for this risk
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
     * Scope untuk filter by company
     */
    public function scopeByCompany($query, string $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope untuk filter by owner
     */
    public function scopeByOwner($query, string $ownerId)
    {
        return $query->where('owner_id', $ownerId);
    }

    /**
     * Scope untuk filter high risk (score >= 15)
     */
    public function scopeHighRisk($query)
    {
        return $query->where('inherent_risk_score', '>=', 15);
    }

    /**
     * Helper Methods
     */

    /**
     * Get risk level based on inherent score
     */
    public function getRiskLevelAttribute(): string
    {
        if ($this->inherent_risk_score >= 20) {
            return 'critical';
        } elseif ($this->inherent_risk_score >= 15) {
            return 'high';
        } elseif ($this->inherent_risk_score >= 10) {
            return 'medium';
        } else {
            return 'low';
        }
    }

    /**
     * Get risk mitigation effectiveness
     */
    public function getMitigationEffectivenessAttribute(): float
    {
        if ($this->residual_risk_score === null || $this->inherent_risk_score === 0) {
            return 0;
        }

        return (($this->inherent_risk_score - $this->residual_risk_score) / $this->inherent_risk_score) * 100;
    }
}

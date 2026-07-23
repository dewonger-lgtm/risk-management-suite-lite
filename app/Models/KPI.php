<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model KPI
 * 
 * Menyimpan definisi Key Performance Indicators (KPI).
 * 
 * @property string $id (UUID)
 * @property string $company_id
 * @property string $department_id
 * @property string $owner_id
 * @property string $code
 * @property string $name
 * @property string $description
 * @property string $formula
 * @property decimal $target_value
 * @property decimal $current_value
 * @property string $unit
 * @property string $frequency
 * @property string $status
 * @property \DateTime $created_at
 * @property \DateTime $updated_at
 * @property \DateTime $deleted_at
 */
class KPI extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * Table name
     */
    protected $table = 'kpis';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'company_id',
        'department_id',
        'owner_id',
        'code',
        'name',
        'description',
        'formula',
        'target_value',
        'current_value',
        'unit',
        'frequency',
        'status',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'target_value' => 'decimal:2',
        'current_value' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Status Enums
     */
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    /**
     * Frequency Enums
     */
    const FREQUENCY_DAILY = 'daily';
    const FREQUENCY_WEEKLY = 'weekly';
    const FREQUENCY_MONTHLY = 'monthly';
    const FREQUENCY_QUARTERLY = 'quarterly';
    const FREQUENCY_YEARLY = 'yearly';

    /**
     * Relationships
     */

    /**
     * Get the company that owns this KPI
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the department this KPI belongs to
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the owner of this KPI
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get all targets for this KPI
     */
    public function targets()
    {
        return $this->hasMany(KPITarget::class, 'kpi_id');
    }

    /**
     * Get all actuals for this KPI
     */
    public function actuals()
    {
        return $this->hasMany(KPIActual::class, 'kpi_id');
    }

    /**
     * Scopes
     */

    /**
     * Scope untuk filter by company
     */
    public function scopeByCompany($query, string $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope untuk filter by department
     */
    public function scopeByDepartment($query, string $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    /**
     * Scope untuk filter active KPIs
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model Company
 * 
 * Menyimpan data perusahaan untuk mendukung multi-company feature.
 * 
 * @property string $id (UUID)
 * @property string $name
 * @property string $code
 * @property string $address
 * @property string $phone
 * @property string $email
 * @property string $logo_path
 * @property bool $is_active
 * @property \DateTime $created_at
 * @property \DateTime $updated_at
 * @property \DateTime $deleted_at
 */
class Company extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * Table name
     */
    protected $table = 'companies';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'code',
        'address',
        'phone',
        'email',
        'logo_path',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Relationships
     */

    /**
     * Get all departments for this company
     */
    public function departments()
    {
        return $this->hasMany(Department::class);
    }

    /**
     * Get all users for this company
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get all risks for this company
     */
    public function risks()
    {
        return $this->hasMany(Risk::class);
    }

    /**
     * Get all incidents for this company
     */
    public function incidents()
    {
        return $this->hasMany(Incident::class);
    }

    /**
     * Get all corrective actions for this company
     */
    public function correctiveActions()
    {
        return $this->hasMany(CorrectiveAction::class);
    }

    /**
     * Get all KPIs for this company
     */
    public function kpis()
    {
        return $this->hasMany(KPI::class);
    }

    /**
     * Get all activity logs for this company
     */
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * Get all approvals for this company
     */
    public function approvals()
    {
        return $this->hasMany(Approval::class);
    }

    /**
     * Get all risk categories for this company
     */
    public function riskCategories()
    {
        return $this->hasMany(RiskCategory::class);
    }
}

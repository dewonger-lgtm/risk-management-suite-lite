<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Model KPIActual
 * 
 * Menyimpan nilai aktual KPI yang tercatat per periode.
 * 
 * @property string $id (UUID)
 * @property string $kpi_id
 * @property string $recorded_by
 * @property string $period
 * @property decimal $actual_value
 * @property \DateTime $recorded_date
 * @property \DateTime $created_at
 * @property \DateTime $updated_at
 */
class KPIActual extends Model
{
    use HasUuids;

    /**
     * Table name
     */
    protected $table = 'kpi_actuals';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'kpi_id',
        'recorded_by',
        'period',
        'actual_value',
        'recorded_date',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'actual_value' => 'decimal:2',
        'recorded_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationships
     */

    /**
     * Get the KPI that owns this actual
     */
    public function kpi()
    {
        return $this->belongsTo(KPI::class);
    }

    /**
     * Get the user who recorded this actual
     */
    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    /**
     * Get the target for this period
     */
    public function target()
    {
        return $this->hasOne(KPITarget::class, 'kpi_id', 'kpi_id')
            ->where('period', $this->period);
    }

    /**
     * Scopes
     */

    /**
     * Scope untuk filter by period
     */
    public function scopeByPeriod($query, string $period)
    {
        return $query->where('period', $period);
    }
}

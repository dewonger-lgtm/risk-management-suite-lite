<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Model KPITarget
 * 
 * Menyimpan target KPI untuk setiap periode waktu.
 * 
 * @property string $id (UUID)
 * @property string $kpi_id
 * @property string $period
 * @property decimal $target_value
 * @property \DateTime $created_at
 * @property \DateTime $updated_at
 */
class KPITarget extends Model
{
    use HasUuids;

    /**
     * Table name
     */
    protected $table = 'kpi_targets';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'kpi_id',
        'period',
        'target_value',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'target_value' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationships
     */

    /**
     * Get the KPI that owns this target
     */
    public function kpi()
    {
        return $this->belongsTo(KPI::class);
    }

    /**
     * Get the actual value for this period
     */
    public function actual()
    {
        return $this->hasOne(KPIActual::class, 'kpi_id', 'kpi_id')
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

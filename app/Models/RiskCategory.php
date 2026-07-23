<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model RiskCategory
 * 
 * Menyimpan kategori risiko yang dapat dikustomisasi per company.
 * 
 * @property string $id (UUID)
 * @property string $company_id
 * @property string $name
 * @property string $description
 * @property string $color
 * @property bool $is_active
 * @property \DateTime $created_at
 * @property \DateTime $updated_at
 * @property \DateTime $deleted_at
 */
class RiskCategory extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * Table name
     */
    protected $table = 'risk_categories';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'company_id',
        'name',
        'description',
        'color',
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
     * Get the company that owns this category
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get all risks in this category
     */
    public function risks()
    {
        return $this->hasMany(Risk::class, 'category_id');
    }
}

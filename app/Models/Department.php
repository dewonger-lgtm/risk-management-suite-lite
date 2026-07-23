<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model Department
 * 
 * Menyimpan struktur organisasi departemen dengan dukungan hierarchical structure.
 * 
 * @property string $id (UUID)
 * @property string $company_id
 * @property string $parent_id
 * @property string $name
 * @property string $code
 * @property string $description
 * @property bool $is_active
 * @property \DateTime $created_at
 * @property \DateTime $updated_at
 * @property \DateTime $deleted_at
 */
class Department extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * Table name
     */
    protected $table = 'departments';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'company_id',
        'parent_id',
        'name',
        'code',
        'description',
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
     * Get the company that owns this department
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the parent department (for hierarchical structure)
     */
    public function parent()
    {
        return $this->belongsTo(Department::class, 'parent_id');
    }

    /**
     * Get all child departments
     */
    public function children()
    {
        return $this->hasMany(Department::class, 'parent_id');
    }

    /**
     * Get all users in this department
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get all KPIs for this department
     */
    public function kpis()
    {
        return $this->hasMany(KPI::class);
    }

    /**
     * Get the full department path (for hierarchical display)
     */
    public function getFullPathAttribute(): string
    {
        $path = [$this->name];
        $parent = $this->parent;

        while ($parent) {
            array_unshift($path, $parent->name);
            $parent = $parent->parent;
        }

        return implode(' > ', $path);
    }
}

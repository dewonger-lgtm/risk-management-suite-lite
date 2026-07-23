<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Model ActivityLog
 * 
 * Menyimpan comprehensive audit trail dari semua aktivitas user.
 * 
 * @property string $id (UUID)
 * @property string $user_id
 * @property string $company_id
 * @property string $action
 * @property string $model
 * @property string $model_id
 * @property array $changes
 * @property string $ip_address
 * @property string $user_agent
 * @property \DateTime $created_at
 */
class ActivityLog extends Model
{
    use HasUuids;

    /**
     * Table name
     */
    protected $table = 'activity_logs';

    /**
     * No updated_at for activity logs
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'company_id',
        'action',
        'model',
        'model_id',
        'changes',
        'ip_address',
        'user_agent',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'changes' => 'json',
        'created_at' => 'datetime',
    ];

    /**
     * Action Enums
     */
    const ACTION_CREATE = 'create';
    const ACTION_UPDATE = 'update';
    const ACTION_DELETE = 'delete';
    const ACTION_VIEW = 'view';
    const ACTION_EXPORT = 'export';
    const ACTION_IMPORT = 'import';

    /**
     * Relationships
     */

    /**
     * Get the user who performed this action
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the company this action belongs to
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Scopes
     */

    /**
     * Scope untuk filter by action
     */
    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope untuk filter by model
     */
    public function scopeByModel($query, string $model)
    {
        return $query->where('model', $model);
    }

    /**
     * Scope untuk filter by company
     */
    public function scopeByCompany($query, string $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope untuk filter by user
     */
    public function scopeByUser($query, string $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope untuk recent logs
     */
    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}

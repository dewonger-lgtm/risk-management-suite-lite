<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Model User
 * 
 * Menyimpan data pengguna aplikasi dengan role-based access control.
 * 
 * @property string $id (UUID)
 * @property string $company_id
 * @property string $department_id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $phone
 * @property string $role
 * @property bool $is_active
 * @property \DateTime $last_login_at
 * @property \DateTime $created_at
 * @property \DateTime $updated_at
 * @property \DateTime $deleted_at
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, HasUuids, Notifiable, SoftDeletes;

    /**
     * Table name
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'company_id',
        'department_id',
        'name',
        'email',
        'password',
        'phone',
        'role',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Role Enums
     */
    const ROLE_ADMINISTRATOR = 'administrator';
    const ROLE_RISK_MANAGER = 'risk_manager';
    const ROLE_AUDITOR = 'auditor';
    const ROLE_DEPARTMENT_HEAD = 'department_head';
    const ROLE_SUPERVISOR = 'supervisor';
    const ROLE_STAFF = 'staff';
    const ROLE_VIEWER = 'viewer';

    /**
     * Get all available roles
     */
    public static function roles(): array
    {
        return [
            self::ROLE_ADMINISTRATOR => 'Administrator',
            self::ROLE_RISK_MANAGER => 'Risk Manager',
            self::ROLE_AUDITOR => 'Auditor',
            self::ROLE_DEPARTMENT_HEAD => 'Department Head',
            self::ROLE_SUPERVISOR => 'Supervisor',
            self::ROLE_STAFF => 'Staff',
            self::ROLE_VIEWER => 'Viewer',
        ];
    }

    /**
     * Relationships
     */

    /**
     * Get the company that owns this user
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the department that owns this user
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get all risks owned by this user
     */
    public function ownedRisks()
    {
        return $this->hasMany(Risk::class, 'owner_id');
    }

    /**
     * Get all risks created by this user
     */
    public function createdRisks()
    {
        return $this->hasMany(Risk::class, 'created_by');
    }

    /**
     * Get all incidents reported by this user
     */
    public function reportedIncidents()
    {
        return $this->hasMany(Incident::class, 'reported_by');
    }

    /**
     * Get all incidents investigated by this user
     */
    public function investigatedIncidents()
    {
        return $this->hasMany(Incident::class, 'investigated_by');
    }

    /**
     * Get all corrective actions assigned to this user
     */
    public function assignedCorrectiveActions()
    {
        return $this->hasMany(CorrectiveAction::class, 'assigned_to');
    }

    /**
     * Get all corrective actions verified by this user
     */
    public function verifiedCorrectiveActions()
    {
        return $this->hasMany(CorrectiveAction::class, 'verified_by');
    }

    /**
     * Get all approvals done by this user
     */
    public function approvals()
    {
        return $this->hasMany(Approval::class, 'approved_by');
    }

    /**
     * Get all KPIs owned by this user
     */
    public function ownedKPIs()
    {
        return $this->hasMany(KPI::class, 'owner_id');
    }

    /**
     * Get all KPI actuals recorded by this user
     */
    public function recordedKPIActuals()
    {
        return $this->hasMany(KPIActual::class, 'recorded_by');
    }

    /**
     * Get all activity logs for this user
     */
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * Get all notifications for this user
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Helper Methods
     */

    /**
     * Check if user is administrator
     */
    public function isAdministrator(): bool
    {
        return $this->role === self::ROLE_ADMINISTRATOR;
    }

    /**
     * Check if user is risk manager
     */
    public function isRiskManager(): bool
    {
        return $this->role === self::ROLE_RISK_MANAGER;
    }

    /**
     * Check if user is auditor
     */
    public function isAuditor(): bool
    {
        return $this->role === self::ROLE_AUDITOR;
    }

    /**
     * Check if user has management role
     */
    public function isManagement(): bool
    {
        return in_array($this->role, [
            self::ROLE_ADMINISTRATOR,
            self::ROLE_RISK_MANAGER,
            self::ROLE_DEPARTMENT_HEAD,
            self::ROLE_AUDITOR,
        ]);
    }
}

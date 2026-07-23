<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Notification
 * 
 * Menyimpan notifikasi untuk users menggunakan Laravel's notification system.
 * 
 * @property string $id (UUID)
 * @property string $user_id
 * @property string $type
 * @property string $notifiable_type
 * @property string $notifiable_id
 * @property array $data
 * @property \DateTime $read_at
 * @property \DateTime $created_at
 */
class Notification extends Model
{
    use HasUuids;

    /**
     * Table name
     */
    protected $table = 'notifications';

    /**
     * No updated_at for notifications
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'type',
        'notifiable_type',
        'notifiable_id',
        'data',
        'read_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'data' => 'json',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    /**
     * Relationships
     */

    /**
     * Get the user this notification belongs to
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the notifiable model (polymorphic)
     */
    public function notifiable()
    {
        return $this->morphTo();
    }

    /**
     * Scopes
     */

    /**
     * Scope untuk filter unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope untuk filter read notifications
     */
    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    /**
     * Scope untuk filter by user
     */
    public function scopeForUser($query, string $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Helper Methods
     */

    /**
     * Mark as read
     */
    public function markAsRead(): void
    {
        if ($this->read_at === null) {
            $this->update(['read_at' => now()]);
        }
    }

    /**
     * Check if notification is read
     */
    public function isRead(): bool
    {
        return $this->read_at !== null;
    }
}

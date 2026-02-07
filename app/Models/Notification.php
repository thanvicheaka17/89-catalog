<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Services\NotificationBroadcastService;
use Carbon\Carbon;
class Notification extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'type',
        'data',
        'user_id',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'data' => 'array',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($notification) {
            $notification->broadcast();
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the read status records for this notification.
     */
    public function readStatuses(): HasMany
    {
        return $this->hasMany(NotificationRead::class);
    }

    /**
     * Check if this notification is read by a specific user.
     */
    public function isReadBy(?string $userId = null): bool
    {
        if (!$userId) {
            return false; // Global notifications are never "read" without user context
        }

        return NotificationRead::isReadByUser($this->id, $userId);
    }

    /**
     * Check if this notification is not read by a specific user.
     */
    public function isNotReadBy(?string $userId = null): bool
    {
        return !$this->isReadBy($userId);
    }

    /**
     * Get the read timestamp for a specific user.
     */
    public function getReadAtForUser(?string $userId = null): ?string
    {
        if (!$userId) {
            return null;
        }

        return NotificationRead::getReadAt($this->id, $userId);
    }

    /**
     * Mark this notification as read for a specific user.
     */
    public function markAsReadForUser(?string $userId = null): ?NotificationRead
    {
        if (!$userId) {
            return null;
        }

        return NotificationRead::markAsRead($this->id, $userId);
    }

    /**
     * Broadcast this notification via websockets.
     */
    public function broadcast(): void
    {
        $broadcastService = app(NotificationBroadcastService::class);
        $broadcastService->broadcast($this);
    }

    /**
     * Broadcast to specific user only.
     */
    public function broadcastToUser(): void
    {
        $broadcastService = app(NotificationBroadcastService::class);
        $broadcastService->broadcastToUser($this);
    }

    /**
     * Broadcast as global announcement.
     */
    public function broadcastGlobally(): void
    {
        $broadcastService = app(NotificationBroadcastService::class);
        $broadcastService->broadcastGlobalAnnouncement($this);
    }

    /**
     * Broadcast as promotion.
     */
    public function broadcastPromotion(): void
    {
        $broadcastService = app(NotificationBroadcastService::class);
        $broadcastService->broadcastPromotion($this);
    }
}

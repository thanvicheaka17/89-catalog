<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class NotificationRead extends Model
{
    use HasFactory, HasUuids;
    protected $fillable = [
        'notification_id',
        'user_id',
        'read_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    /**
     * Get the notification that this read status belongs to.
     */
    public function notification(): BelongsTo
    {
        return $this->belongsTo(Notification::class);
    }

    /**
     * Get the user that read this notification.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mark a notification as read for a specific user.
     */
    public static function markAsRead(string $notificationId, string $userId): static
    {
        return static::updateOrCreate(
            [
                'notification_id' => $notificationId,
                'user_id' => $userId,
            ],
            [
                'read_at' => now(),
            ]
        );
    }

    /**
     * Check if a notification is read by a specific user.
     */
    public static function isReadByUser(string $notificationId, string $userId): bool
    {
        return static::where('notification_id', $notificationId)
            ->where('user_id', $userId)
            ->exists();
    }

    /**
     * Get the read timestamp for a specific notification and user.
     */
    public static function getReadAt(string $notificationId, string $userId): ?string
    {
        $read = static::where('notification_id', $notificationId)
            ->where('user_id', $userId)
            ->first();

        return $read?->read_at?->toISOString();
    }
}

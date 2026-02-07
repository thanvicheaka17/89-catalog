<?php

namespace App\Services;

use App\Events\GlobalNotificationBroadcast;
use App\Models\Notification;
use Illuminate\Support\Facades\Log;

class NotificationBroadcastService
{
    /**
     * Broadcast a notification based on its type.
     */
    public function broadcast(Notification $notification): void
    {
        try {
            // Use GlobalNotificationBroadcast for all supported notification types
            switch ($notification->type) {
                case 'promotion':
                case 'global':
                case 'event':
                case 'testimonial':
                case 'announcement':
                    $this->broadcastGlobalAnnouncement($notification);
                    break;
                default:
                    Log::info('Notification type not configured for broadcasting', [
                        'notification_id' => $notification->id,
                        'type' => $notification->type
                    ]);
                    break;
            }
        } catch (\Exception $e) {
            Log::error('Failed to broadcast notification', [
                'notification_id' => $notification->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Broadcast to specific user channels.
     */
    public function broadcastToUser(Notification $notification): void
    {
        try {
            // Broadcast to user's private channel
            
        } catch (\Exception $e) {
            Log::error('Failed to broadcast notification to user', [
                'notification_id' => $notification->id,
                'user_id' => $notification->user_id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Broadcast notification using GlobalNotificationBroadcast (handles all types by type).
     */
    public function broadcastGlobalAnnouncement(Notification $notification): void
    {
        try {
            broadcast(new GlobalNotificationBroadcast($notification))->toOthers();
        } catch (\Exception $e) {
            Log::error('Failed to broadcast notification', [
                'notification_id' => $notification->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}

<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\NotificationRead;
use App\Services\NotificationBroadcastService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    /**
     * Check if a notification should be shown to the user based on push notification preference.
     */
    private function isUserEnabledNotification(Notification $notification, $user): bool
    {
        // Current user must have push notifications enabled to see any notifications
        if (!$user || !$user->push_notifications) {
            return false;
        }

        // If notification has a user_id, check that target user's push notification preference
        if ($notification->user_id) {
            $notificationUser = $notification->user;
            if (!$notificationUser) {
                return false;
            }
            return $notificationUser->push_notifications === true;
        }

        // For global notifications (no user_id), current user already checked above
        return true;
    }

    public function index(Request $request): JsonResponse
    {
        $user = auth('api')->user();
        $perPage = $request->input('per_page', 10);
        
        // Filter notifications based on push notification preference
        $query = Notification::query();
        
        // If user has push notifications disabled, filter out notifications
        if ($user && !$user->push_notifications) {
            // Don't show any notifications if user has push notifications disabled
            $query->whereRaw('1 = 0'); // Return empty result
        } else {
            // Filter: show global notifications (user_id is null) or user-specific notifications where user has push enabled
            $query->where(function ($q) use ($user) {
                // Global notifications (user_id is null)
                $q->whereNull('user_id');
                
                // User-specific notifications where the target user has push notifications enabled
                if ($user) {
                    $q->orWhereHas('user', function ($userQuery) {
                        $userQuery->where('push_notifications', true);
                    });
                }
            });
        }
        
        $notifications = $query->orderBy('created_at', 'desc')->paginate($perPage);

        $data = $notifications->map(function ($notification) use ($user) {
            return [
                'id' => $notification->id,
                'type' => $notification->type,
                'data' => $notification->data,
                'is_read' => $notification->isReadBy($user?->id),
                'read_at' => $notification->getReadAtForUser($user?->id),
                'created_at' => $notification->created_at
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'total' => $notifications->total(),
            'current_page' => $notifications->currentPage(),
            'last_page' => $notifications->lastPage(),
            'per_page' => $notifications->perPage(),
        ]);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $user = auth('api')->user();
        $notification = Notification::findOrFail($id);

        // Check if notification should be shown based on push notification preference
        if (!$this->isUserEnabledNotification($notification, $user)) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not available',
            ], 404);
        }

        $data = [
            'id' => $notification->id,
            'type' => $notification->type,
            'data' => $notification->data,
            'is_read' => $notification->isReadBy($user?->id),
            'read_at' => $notification->getReadAtForUser($user?->id),
            'created_at' => $notification->created_at,
        ];

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function read(Request $request): JsonResponse
    {
        $user = auth('api')->user();
        $notificationId = $request->input('id');

        // Find the notification (works for both user-specific and global notifications)
        $notification = Notification::findOrFail($notificationId);

        // Check if notification should be shown based on push notification preference
        if (!$this->isUserEnabledNotification($notification, $user)) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not available',
            ], 404);
        }

        // Mark as read for this specific user
        $readStatus = $notification->markAsReadForUser($user?->id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $notification->id,
                'type' => $notification->type,
                'data' => $notification->data,
                'is_read' => true,
                'read_at' => $readStatus?->read_at?->toISOString(),
                'created_at' => $notification->created_at,
            ],
        ]);
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        $user = auth('api')->user();

        // Get notifications that should be shown to the user
        $query = Notification::query();
        
        // Filter based on push notification preference
        if ($user && !$user->push_notifications) {
            // If user has push notifications disabled, no notifications to mark
            $allNotifications = collect();
        } else {
            $query->where(function ($q) use ($user) {
                // Global notifications (user_id is null)
                $q->whereNull('user_id');
                
                // User-specific notifications where the target user has push notifications enabled
                if ($user) {
                    $q->orWhereHas('user', function ($userQuery) {
                        $userQuery->where('push_notifications', true);
                    });
                }
            });
            
            $allNotifications = $query->get();
        }

        $markedCount = 0;
        foreach ($allNotifications as $notification) {
            // Only mark as read if not already read by this user and should be shown
            if ($this->isUserEnabledNotification($notification, $user) && !$notification->isReadBy($user->id)) {
                $notification->markAsReadForUser($user->id);
                $markedCount++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read',
            'marked_count' => $markedCount,
            'total_notifications' => $allNotifications->count(),
        ]);
    }

    public function unread(Request $request): JsonResponse
    {
        $user = auth('api')->user();
        $perPage = $request->input('per_page', 10);
        
        // Build query for unread notifications
        $query = Notification::whereDoesntHave('readStatuses', function ($q) use ($user) {
            $q->where('user_id', $user?->id);
        });
        
        // Filter based on push notification preference
        if ($user && !$user->push_notifications) {
            // If user has push notifications disabled, return empty result
            $query->whereRaw('1 = 0');
        } else {
            // Filter: show global notifications (user_id is null) or user-specific notifications where user has push enabled
            $query->where(function ($q) use ($user) {
                // Global notifications (user_id is null)
                $q->whereNull('user_id');
                
                // User-specific notifications where the target user has push notifications enabled
                if ($user) {
                    $q->orWhereHas('user', function ($userQuery) {
                        $userQuery->where('push_notifications', true);
                    });
                }
            });
        }
        
        $notifications = $query->orderBy('created_at', 'desc')->paginate($perPage);

        $data = $notifications->map(function ($notification) use ($user) {
            return [
                'id' => $notification->id,
                'type' => $notification->type,
                'data' => $notification->data,
                'is_read' => $notification->isReadBy($user?->id),
                'read_at' => $notification->getReadAtForUser($user?->id),
                'created_at' => $notification->created_at
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'total' => $notifications->total(),
            'current_page' => $notifications->currentPage(),
            'last_page' => $notifications->lastPage(),
            'per_page' => $notifications->perPage(),
        ]);
    }
}

<?php

namespace App\Events;

use App\Models\Notification;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class GlobalNotificationBroadcast implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Notification $notification;
    public array $broadcastData;

    /**
     * Create a new event instance.
     */
    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
        $this->broadcastData = $this->prepareBroadcastData();
    }

    /**
     * Prepare the broadcast data for global notifications.
     */
    private function prepareBroadcastData(): array
    {
        $currentDateTime = Carbon::now();

        // Handle different notification types
        if ($this->notification->type === 'promotion') {
            return $this->preparePromotionBroadcastData($currentDateTime);
        } elseif ($this->notification->type === 'event') {
            return $this->prepareEventBroadcastData($currentDateTime);
        } elseif ($this->notification->type === 'testimonial') {
            return $this->prepareTestimonialBroadcastData($currentDateTime);
        } elseif ($this->notification->type === 'hot_and_fresh') {
            return $this->prepareHotAndFreshBroadcastData($currentDateTime);
        } elseif ($this->notification->type === 'demo_game') {
            return $this->prepareDemoGameBroadcastData($currentDateTime);
        } elseif ($this->notification->type === 'top_tier_tool') {
            return $this->prepareTopTierToolBroadcastData($currentDateTime);
        } elseif ($this->notification->type === 'casino') {
            return $this->prepareCasinoBroadcastData($currentDateTime);
        }

        // Generate different broadcast variants for global notifications
        $variants = [
            'basic' => [
                'title' => '📢 Announcement!',
                'message' => $this->notification->message ?? 'Important update available',
                'type' => $this->notification->type ?? 'global'
            ],
            'detailed' => [
                'title' => '📢 GLOBAL ANNOUNCEMENT',
                'message' => "📅 {$currentDateTime->format('d/m/Y')} 🕐 {$currentDateTime->format('H:i')}\n📢 " . ($this->notification->title ?? 'Important Notice') . "\n" . ($this->notification->message ? "💬 {$this->notification->message}\n" : "") . "🎯 Stay informed",
                'type' => $this->notification->type ?? 'global'
            ],
            'compact' => [
                'title' => '📢 ' . ($this->notification->title ?? 'Global Notification'),
                'message' => "✨ Important announcement available!",
                'type' => $this->notification->type ?? 'global'
            ]
        ];

        return [
            'id' => $this->notification->id, // Match API field name
            'type' => $this->notification->type,
            'data' => $this->notification->data,
            'created_at' => $this->notification->created_at->toISOString(), // Match API field
            'timestamp' => $currentDateTime->toISOString(),
            'title' => $this->notification->title,
            'message' => $this->notification->message,
            'user_id' => $this->notification->user_id,
            'variants' => $variants,
            'broadcast_at' => $currentDateTime->format('d M Y H:i'),
            'priority' => 'high',
            'notification_id' => $this->notification->id, // Keep for backward compatibility
            // Add API-compatible fields
            'is_read' => false, // New notifications are unread
            'read_at' => null,
        ];
    }

    /**
     * Prepare broadcast data specifically for promotion notifications.
     */
    private function preparePromotionBroadcastData(Carbon $currentDateTime): array
    {
        // Get promotion from notification data
        $promotionData = $this->notification->data ?? [];
        $promotionId = $promotionData['promotion_id'] ?? null;

        // For now, we'll use the notification data directly
        // In a real implementation, you might want to load the Promotion model
        $promotion = $promotionData;

        // Generate different broadcast variants for promotions
        $variants = [
            'basic' => [
                'title' => '🎉 New Promotion!',
                'message' => "📢 " . ($promotion['title'] ?? $this->notification->title) . " - Check it out now!",
                'type' => 'promotion'
            ],
            'detailed' => [
                'title' => '🚀 NEW PROMOTION ALERT',
                'message' => "📅 {$currentDateTime->format('d/m/Y')} 🕐 {$currentDateTime->format('H:i')}\n📢 " . ($promotion['title'] ?? $this->notification->title) . "\n" . (($promotion['message'] ?? $this->notification->message) ? "💬 " . ($promotion['message'] ?? $this->notification->message) . "\n" : "") . "🎯 Click to view details",
                'type' => 'promotion'
            ],
            'compact' => [
                'title' => '🎁 Promotion: ' . ($promotion['title'] ?? $this->notification->title),
                'message' => "✨ New exciting promotion available!",
                'type' => 'promotion'
            ]
        ];

        return [
            'id' => $this->notification->id, // API-compatible field
            'type' => 'promotion',
            'data' => $this->notification->data,
            'created_at' => $this->notification->created_at->toISOString(), // API-compatible field
            'is_read' => false, // New notifications are unread
            'read_at' => null,
            'timestamp' => $currentDateTime->toISOString(),
            // Promotion-specific data
            'promotion_id' => $promotionId,
            'title' => $promotion['title'] ?? $this->notification->title,
            'message' => $promotion['message'] ?? $this->notification->message,
            'button_text' => $promotion['button_text'] ?? null,
            'button_url' => $promotion['button_url'] ?? null,
            'image_url' => $promotion['image_url'] ?? null,
            'position' => $promotion['position'] ?? null,
            'is_active' => $promotion['is_active'] ?? true,
            'priority' => $promotion['priority'] ?? 'normal',
            'creator_name' => $promotion['creator_name'] ?? 'Admin',
            'user_id' => $this->notification->user_id,
            'variants' => $variants,
            'broadcast_at' => $currentDateTime->format('d M Y H:i'),
            // Backward compatibility
            'notification_id' => $this->notification->id,
        ];
    }

    /**
     * Prepare broadcast data specifically for event notifications.
     */
    private function prepareEventBroadcastData(Carbon $currentDateTime): array
    {
        // Get event from notification data
        $eventData = $this->notification->data ?? [];
        $eventId = $eventData['event_id'] ?? null;

        // For now, we'll use the notification data directly
        // In a real implementation, you might want to load the Promotion model
        $event = $eventData;

        // Generate different broadcast variants for events
        $variants = [
            'basic' => [
                'title' => '🎉 New Event!',
                'message' => "📢 " . ($event['title'] ?? $this->notification->title) . " - Check it out now!",
                'type' => 'event'
            ],
            'detailed' => [
                'title' => '🚀 NEW EVENT ALERT',
                'message' => "📅 {$currentDateTime->format('d/m/Y')} 🕐 {$currentDateTime->format('H:i')}\n📢 " . ($event['title'] ?? $this->notification->title) . "\n" . (($event['message'] ?? $this->notification->message) ? "💬 " . ($event['message'] ?? $this->notification->message) . "\n" : "") . "🎯 Click to view details",
                'type' => 'event'
            ],
            'compact' => [
                'title' => '🎁 Event: ' . ($event['title'] ?? $this->notification->title),
                'message' => "✨ New exciting event available!",
                'type' => 'event'
            ]
        ];

        return [
            'id' => $this->notification->id, // API-compatible field
            'type' => 'event',
            'data' => $this->notification->data,
            'created_at' => $this->notification->created_at->toISOString(), // API-compatible field
            'is_read' => false, // New notifications are unread
            'read_at' => null,
            'timestamp' => $currentDateTime->toISOString(),
            // Event-specific data
            'event_id' => $eventId,
            'title' => $event['title'] ?? $this->notification->title,
            'message' => $event['message'] ?? $this->notification->message,
            'description' => $event['description'] ?? $this->notification->description,
            'start_at' => $event['start_at'] ?? $this->notification->start_at,
            'end_at' => $event['end_at'] ?? $this->notification->end_at,
            'is_active' => $event['is_active'] ?? true,
            'priority' => $event['priority'] ?? 'normal',
            'creator_name' => $event['creator_name'] ?? 'Admin',
            'user_id' => $this->notification->user_id,
            'variants' => $variants,
            'broadcast_at' => $currentDateTime->format('d M Y H:i'),
            // Backward compatibility
            'notification_id' => $this->notification->id,
        ];
    }

    /**
     * Prepare broadcast data specifically for testimonial notifications.
     */
    private function prepareTestimonialBroadcastData(Carbon $currentDateTime): array
    {
        // Get testimonial from notification data
        $testimonialData = $this->notification->data ?? [];
        $testimonialId = $testimonialData['testimonial_id'] ?? null;

        // For now, we'll use the notification data directly
        // In a real implementation, you might want to load the Promotion model
        $testimonial = $testimonialData;

        // Generate different broadcast variants for events
        $variants = [
            'basic' => [
                'title' => '🎉 New Testimonial!',
                'message' => "📢 " . ($testimonial['user_name'] ?? $this->notification->title) . " - Check it out now!",
                'type' => 'testimonial'
            ],
            'detailed' => [
                'title' => '🚀 NEW TESTIMONIAL ALERT',
                'message' => "📅 {$currentDateTime->format('d/m/Y')} 🕐 {$currentDateTime->format('H:i')}\n📢 " . ($testimonial['user_name'] ?? $this->notification->title) . "\n" . (($testimonial['message'] ?? $this->notification->message) ? "💬 " . ($testimonial['message'] ?? $this->notification->message) . "\n" : "") . "🎯 Click to view details",
                'type' => 'testimonial'
            ],
            'compact' => [
                'title' => '🎁 Testimonial: ' . ($testimonial['user_name'] ?? $this->notification->title),
                'message' => "✨ New exciting testimonial available!",
                'type' => 'testimonial'
            ]
        ];

        return [
            'id' => $this->notification->id, // API-compatible field
            'type' => 'testimonial',
            'data' => $this->notification->data,
            'created_at' => $this->notification->created_at->toISOString(), // API-compatible field
            'is_read' => false, // New notifications are unread
            'read_at' => null,
            'timestamp' => $currentDateTime->toISOString(),
            // Testimonial-specific data
            'testimonial_id' => $testimonialId,
            'title' => $testimonial['user_name'] ?? $this->notification->title,
            'message' => $testimonial['message'] ?? $this->notification->message,
            'rating' => $testimonial['rating'] ?? $this->notification->rating,
            'user_id' => $this->notification->user_id,
            'variants' => $variants,
            'broadcast_at' => $currentDateTime->format('d M Y H:i'),
            // Backward compatibility
            'notification_id' => $this->notification->id,
        ];
    }

    /**
     * Prepare broadcast data specifically for hot and fresh notifications.
     */
    private function prepareHotAndFreshBroadcastData(Carbon $currentDateTime): array
    {
        // Get testimonial from notification data
        $hotAndFreshData = $this->notification->data ?? [];
        $hotAndFreshId = $hotAndFreshData['hot_and_fresh_id'] ?? null;

        // For now, we'll use the notification data directly
        // In a real implementation, you might want to load the Promotion model
        $hotAndFresh = $hotAndFreshData;

        // Generate different broadcast variants for events
        $variants = [
            'basic' => [
                'title' => '🎉 New Hot & Fresh Game Available!',
                'message' => "📢 " . ($hotAndFresh['name'] ?? $this->notification->title) . " - Check it out now!",
                'type' => 'hot_and_fresh'
            ],
            'detailed' => [
                'title' => '🚀 NEW HOT & FRESH GAME ALERT',
                'message' => "📅 {$currentDateTime->format('d/m/Y')} 🕐 {$currentDateTime->format('H:i')}\n📢 " . ($hotAndFresh['name'] ?? $this->notification->title) . "\n" . (($hotAndFresh['message'] ?? $this->notification->message) ? "💬 " . ($hotAndFresh['message'] ?? $this->notification->message) . "\n" : "") . "🎯 Click to view details",
                'type' => 'hot_and_fresh'
            ],
            'compact' => [
                'title' => '🎁 Hot & Fresh Game: ' . ($hotAndFresh['name'] ?? $this->notification->title),
                'message' => "✨ New exciting hot and fresh game available!",
                'type' => 'hot_and_fresh'
            ]
        ];

        return [
            'id' => $this->notification->id, // API-compatible field
            'type' => 'hot_and_fresh',
            'data' => $this->notification->data,
            'created_at' => $this->notification->created_at->toISOString(), // API-compatible field
            'is_read' => false, // New notifications are unread
            'read_at' => null,
            'timestamp' => $currentDateTime->toISOString(),
            // Hot & Fresh-specific data
            'hot_and_fresh_id' => $hotAndFreshId,
            'title' => $hotAndFresh['name'] ?? $this->notification->title,
            'message' => $hotAndFresh['message'] ?? $this->notification->message,
            'rating' => $hotAndFresh['rating'] ?? $this->notification->rating,
            'user_id' => $this->notification->user_id,
            'variants' => $variants,
            'broadcast_at' => $currentDateTime->format('d M Y H:i'),
            // Backward compatibility
            'notification_id' => $this->notification->id,
        ];
    }

    /**
     * Prepare broadcast data specifically for demo game notifications.
     */
    private function prepareDemoGameBroadcastData(Carbon $currentDateTime): array
    {
        // Get testimonial from notification data
        $demoGameData = $this->notification->data ?? [];
        $demoGameId = $demoGameData['demo_game_id'] ?? null;

        // For now, we'll use the notification data directly
        // In a real implementation, you might want to load the Promotion model
        $demoGame = $demoGameData;

        // Generate different broadcast variants for events
        $variants = [
            'basic' => [
                'title' => '🎯 New ' . ($demoGame['is_demo'] ? 'Demo Game' : 'Game') . ' Available!',
                'message' => "📢 " . ($demoGame['title'] ?? $this->notification->title) . " - Check it out now!",
                'type' => 'demo_game'
            ],
            'detailed' => [
                'title' => '🚀 NEW ' . ($demoGame['is_demo'] ? 'DEMO GAME' : 'GAME') . ' ALERT',
                'message' => "📅 {$currentDateTime->format('d/m/Y')} 🕐 {$currentDateTime->format('H:i')}\n📢 " . ($hotAndFresh['name'] ?? $this->notification->title) . "\n" . (($hotAndFresh['message'] ?? $this->notification->message) ? "💬 " . ($hotAndFresh['message'] ?? $this->notification->message) . "\n" : "") . "🎯 Click to view details",
                'type' => 'demo_game'
            ],
            'compact' => [
                'title' => '🎁 ' . ($demoGame['is_demo'] ? 'Demo Game' : 'Game') . ': ' . ($demoGame['title'] ?? $this->notification->title),
                'message' => "✨ New exciting " . ($demoGame['is_demo'] ? 'Demo Game' : 'Game') . " available!",
                'type' => 'demo_game'
            ]
        ];

        return [
            'id' => $this->notification->id, // API-compatible field
            'type' => 'demo_game',
            'data' => $this->notification->data,
            'created_at' => $this->notification->created_at->toISOString(), // API-compatible field
            'is_read' => false, // New notifications are unread
            'read_at' => null,
            'timestamp' => $currentDateTime->toISOString(),
            // Demo Game-specific data
            'demo_game_id' => $demoGameId,
            'title' => $demoGame['title'] ?? $this->notification->title,
            'message' => $demoGame['message'] ?? $this->notification->message,
            'rating' => $demoGame['rating'] ?? $this->notification->rating,
            'user_id' => $this->notification->user_id,
            'variants' => $variants,
            'broadcast_at' => $currentDateTime->format('d M Y H:i'),
            // Backward compatibility
            'notification_id' => $this->notification->id,
        ];
    }

    /**
     * Prepare broadcast data specifically for top tier tool notifications.
     */
    private function prepareTopTierToolBroadcastData(Carbon $currentDateTime): array
    {
        // Get testimonial from notification data
        $topTierToolData = $this->notification->data ?? [];
        $topTierToolId = $topTierToolData['top_tier_tool_id'] ?? null;

        // For now, we'll use the notification data directly
        // In a real implementation, you might want to load the Promotion model
        $topTierTool = $topTierToolData;

        // Generate different broadcast variants for events
        $variants = [
            'basic' => [
                'title' => '🎯 New ' . ($topTierTool['tier'] ? ucfirst($topTierTool['tier']) . ' ' : '') . 'Tool Available!',
                'message' => "📢 " . ($topTierTool['name'] ?? $this->notification->title) . " - Check it out now!",
                'type' => 'top_tier_tool'
            ],
            'detailed' => [
                'title' => '🚀 NEW ' . ($topTierTool['tier'] ? ucfirst($topTierTool['tier']) . ' ' : '') . 'TOOL ALERT',
                'message' => "📅 {$currentDateTime->format('d/m/Y')} 🕐 {$currentDateTime->format('H:i')}\n📢 " . ($topTierTool['name'] ?? $this->notification->title) . "\n" . (($topTierTool['message'] ?? $this->notification->message) ? "💬 " . ($topTierTool['message'] ?? $this->notification->message) . "\n" : "") . "🎯 Click to view details",
                'type' => 'top_tier_tool'
            ],
            'compact' => [
                'title' => '🎁 ' . ($topTierTool['tier'] ? ucfirst($topTierTool['tier']) . ' ' : '') . 'Tool: ' . ($topTierTool['name'] ?? $this->notification->title),
                'message' => "✨ New exciting " . ($topTierTool['tier'] ? ucfirst($topTierTool['tier']) . ' ' : '') . " tool available!",
                'type' => 'top_tier_tool'
            ]
        ];

        return [
            'id' => $this->notification->id, // API-compatible field
            'type' => 'top_tier_tool',
            'data' => $this->notification->data,
            'created_at' => $this->notification->created_at->toISOString(), // API-compatible field
            'is_read' => false, // New notifications are unread
            'read_at' => null,
            'timestamp' => $currentDateTime->toISOString(),
            // Top Tier Tool-specific data
            'top_tier_tool_id' => $topTierToolId,
            'title' => $topTierTool['name'] ?? $this->notification->title,
            'message' => $topTierTool['message'] ?? $this->notification->message,
            'rating' => $topTierTool['rating'] ?? $this->notification->rating,
            'user_id' => $this->notification->user_id,
            'variants' => $variants,
            'broadcast_at' => $currentDateTime->format('d M Y H:i'),
            // Backward compatibility
            'notification_id' => $this->notification->id,
        ];
    }

    /**
     * Prepare broadcast data specifically for casino notifications.
     */
    private function prepareCasinoBroadcastData(Carbon $currentDateTime): array
    {
        // Get testimonial from notification data
        $casinoData = $this->notification->data ?? [];
        $casinoId = $casinoData['casino_id'] ?? null;

        // For now, we'll use the notification data directly
        // In a real implementation, you might want to load the Promotion model
        $casino = $casinoData;

        // Generate different broadcast variants for events
        $variants = [
            'basic' => [
                'title' => '🎰 New Casino Just Launched!',
                'message' => "📢 " . ($casino['name'] ?? $this->notification->title) . " - Check it out now!",
                'type' => 'casino'
            ],
            'detailed' => [
                'title' => '🚀 NEW CASINO ALERT',
                'message' => "📅 {$currentDateTime->format('d/m/Y')} 🕐 {$currentDateTime->format('H:i')}\n📢 " . ($casino['name'] ?? $this->notification->title) . "\n" . (($casino['message'] ?? $this->notification->message) ? "💬 " . ($casino['message'] ?? $this->notification->message) . "\n" : "") . "🎯 Click to view details",
                'type' => 'casino'
            ],
            'compact' => [
                'title' => '🎁 Casino: ' . ($casino['name'] ?? $this->notification->title),
                'message' => "✨ New exciting casino just launched!",
                'type' => 'casino'
            ]
        ];

        return [
            'id' => $this->notification->id, // API-compatible field
            'type' => 'casino',
            'data' => $this->notification->data,
            'created_at' => $this->notification->created_at->toISOString(), // API-compatible field
            'is_read' => false, // New notifications are unread
            'read_at' => null,
            'timestamp' => $currentDateTime->toISOString(),
            // Casino-specific data
            'casino_id' => $casinoId,
            'title' => $casino['name'] ?? $this->notification->title,
            'message' => $casino['message'] ?? $this->notification->message,
            'rating' => $casino['rating'] ?? $this->notification->rating,
            'user_id' => $this->notification->user_id,
            'variants' => $variants,
            'broadcast_at' => $currentDateTime->format('d M Y H:i'),
            // Backward compatibility
            'notification_id' => $this->notification->id,
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('notifications.global'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'global.notification';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return $this->broadcastData;
    }

    /**
     * Determine if this event should broadcast.
     */
    public function broadcastWhen(): bool
    {
        // Only broadcast if broadcasting is enabled and data is prepared
        if (config('broadcasting.default') === 'null' || empty($this->broadcastData)) {
            return false;
        }

        // If notification has a user_id, check if user has push notifications enabled
        if ($this->notification->user_id) {
            $user = $this->notification->user;
            
            // If user doesn't exist, don't broadcast
            if (!$user) {
                return false;
            }

            // Only broadcast if user has push notifications enabled
            return $user->push_notifications === true;
        }

        // For global notifications (no user_id), broadcast to all
        return true;
    }
}
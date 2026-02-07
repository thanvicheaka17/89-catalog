<?php

namespace App\Events;

use App\Models\ChatMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class ChatMessageBroadcast implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ChatMessage $chatMessage;
    public array $broadcastData;

    /**
     * Create a new event instance.
     */
    public function __construct(ChatMessage $chatMessage)
    {
        $this->chatMessage = $chatMessage;
        $this->broadcastData = $this->prepareBroadcastData();
    }

    /**
     * Prepare the broadcast data for the chat message.
     */
    private function prepareBroadcastData(): array
    {
        return [
            'id' => $this->chatMessage->id,
            'user_id' => $this->chatMessage->user_id,
            'username' => $this->chatMessage->masked_username, // Use masked username for privacy
            'message' => $this->chatMessage->message,
            'timestamp' => $this->chatMessage->created_at->toISOString(),
            'formatted_time' => $this->chatMessage->formatted_time,
            'type' => 'chat_message'
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
            new Channel('chat'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'chat.message';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return $this->broadcastData;
    }
}

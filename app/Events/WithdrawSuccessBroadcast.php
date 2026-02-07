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

class WithdrawSuccessBroadcast implements ShouldBroadcast
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
     * Prepare the broadcast data with notification templates.
     */
    private function prepareBroadcastData(): array
    {
        $user = $this->notification->user;
        $maskedUsername = $this->maskUsername($user->name ?? $user->username);
        $formattedAmount = $this->formatAmount($this->notification->amount);
        $currentDateTime = Carbon::now();

        // Generate different broadcast variants
        $variants = [
            'basic' => [
                'title' => '✅ Withdraw Successful!',
                'message' => "👤 {$maskedUsername} successfully withdrew Rp {$formattedAmount}",
                'type' => 'withdraw_success'
            ],
            'detailed' => [
                'title' => '🎉 LIVE NOTIFICATION',
                'message' => "📅 {$currentDateTime->format('d/m/Y')} 🕐 {$currentDateTime->format('H:i')}\n👤 {$maskedUsername} successfully withdrew Rp {$formattedAmount}\n🔍 View Transfer Proof",
                'type' => 'withdraw_success'
            ],
            'compact' => [
                'title' => '💰 Withdrawal Rp ' . $formattedAmount . ' – View Proof',
                'message' => "🎯 Payment Confirmed – Check Proof",
                'type' => 'withdraw_success'
            ],
            'jackpot' => [
                'title' => '💎 Jackpot Withdrawal',
                'message' => "Big win! {$maskedUsername} withdrew Rp {$formattedAmount}",
                'type' => 'jackpot_withdraw'
            ]
        ];

        return [
            'notification_id' => $this->notification->id,
            'user_id' => $user->id,
            'masked_username' => $maskedUsername,
            'amount' => $this->notification->amount,
            'formatted_amount' => $formattedAmount,
            'proof_url' => $this->notification->proof_url,
            'timestamp' => $currentDateTime->toISOString(),
            'variants' => $variants,
            'broadcast_at' => $currentDateTime->format('d M Y H:i'),
        ];
    }

    /**
     * Mask username for privacy (show first 4 chars + **XX)
     */
    private function maskUsername(string $username): string
    {
        if (strlen($username) <= 4) {
            return $username . '**XX';
        }

        return substr($username, 0, 4) . '**XX';
    }

    /**
     * Format amount with thousand separators
     */
    private function formatAmount(float $amount): string
    {
        return number_format($amount, 0, ',', '.');
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('withdraw-success'),
            new Channel('notifications.global'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'withdraw.success';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return $this->broadcastData;
    }
}

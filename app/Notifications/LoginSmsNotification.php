<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Services\IpGeolocationService;

class LoginSmsNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $user;
    public $ip;
    public $userAgent;
    public $loginTime;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $user, string $ip, string $userAgent)
    {
        $this->user = $user;
        $this->ip = $ip;
        $this->userAgent = $userAgent;
        $this->loginTime = now();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['sms'];
    }

    /**
     * Get the SMS representation of the notification.
     */
    public function toSms(object $notifiable): string
    {
        $appName = config('app.name', 'Laravel');
        $time = $this->loginTime->format('M j, Y g:i A');
        $location = $this->getLocationFromIP($this->ip);
        $device = $this->getDeviceInfo($this->userAgent);

        return "🔐 New login to {$appName}\n\n" .
               "👤 User: {$this->user->name}\n" .
               "🕐 Time: {$time}\n" .
               "📍 Location: {$location}\n" .
               "📱 Device: {$device}\n\n" .
               "If this wasn't you, please secure your account immediately.";
    }

    /**
     * Get location information from IP address.
     */
    private function getLocationFromIP(string $ip): string
    {
        $geolocationService = app(IpGeolocationService::class);
        return $geolocationService->getLocation($ip);
    }

    /**
     * Get device information from user agent.
     */
    private function getDeviceInfo(string $userAgent): string
    {
        // Simple device detection for SMS
        if (stripos($userAgent, 'Mobile') !== false) {
            return 'Mobile Device';
        } elseif (stripos($userAgent, 'Tablet') !== false) {
            return 'Tablet';
        } else {
            return 'Desktop';
        }
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'ip' => $this->ip,
            'user_agent' => $this->userAgent,
            'login_time' => $this->loginTime,
            'type' => 'login_notification',
        ];
    }
}

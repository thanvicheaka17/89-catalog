<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;
use App\Models\SiteSetting;
use App\Services\IpGeolocationService;
class LoginNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $ip;
    public $userAgent;
    public $loginTime;

    /**
     * Create a new message instance.
     */
    public function __construct($user, string $ip, string $userAgent)
    {
        $this->user = $user;
        $this->ip = $ip;
        $this->userAgent = $userAgent;
        $this->loginTime = now();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $appName = SiteSetting::where('key', 'site_name')->first()->value ?? config('app.name');
        
        return new Envelope(
            subject: 'New Login to Your Account - ' . $appName,
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            to: [new Address($this->user->email)],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.login-notification',
            with: [
                'user' => $this->user,
                'ip' => $this->ip,
                'userAgent' => $this->userAgent,
                'loginTime' => $this->loginTime,
                'location' => $this->getLocationFromIP($this->ip),
                'deviceInfo' => $this->parseUserAgent($this->userAgent),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
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
     * Parse user agent to get device information.
     */
    private function parseUserAgent(string $userAgent): array
    {
        // Basic user agent parsing - you might want to use a proper library like:
        // - jenssegers/agent
        // - ua-parser/uap-php

        $deviceInfo = [
            'browser' => 'Unknown',
            'os' => 'Unknown',
            'device' => 'Unknown',
        ];

        // Simple browser detection
        if (stripos($userAgent, 'Chrome') !== false && stripos($userAgent, 'Edg') === false) {
            $deviceInfo['browser'] = 'Chrome';
        } elseif (stripos($userAgent, 'Firefox') !== false) {
            $deviceInfo['browser'] = 'Firefox';
        } elseif (stripos($userAgent, 'Safari') !== false && stripos($userAgent, 'Chrome') === false) {
            $deviceInfo['browser'] = 'Safari';
        } elseif (stripos($userAgent, 'Edg') !== false) {
            $deviceInfo['browser'] = 'Edge';
        }

        // Simple OS detection
        if (stripos($userAgent, 'Windows') !== false) {
            $deviceInfo['os'] = 'Windows';
        } elseif (stripos($userAgent, 'Mac OS X') !== false) {
            $deviceInfo['os'] = 'macOS';
        } elseif (stripos($userAgent, 'Linux') !== false) {
            $deviceInfo['os'] = 'Linux';
        } elseif (stripos($userAgent, 'Android') !== false) {
            $deviceInfo['os'] = 'Android';
        } elseif (stripos($userAgent, 'iPhone') !== false || stripos($userAgent, 'iPad') !== false) {
            $deviceInfo['os'] = 'iOS';
        }

        // Simple device detection
        if (stripos($userAgent, 'Mobile') !== false) {
            $deviceInfo['device'] = 'Mobile';
        } elseif (stripos($userAgent, 'Tablet') !== false || stripos($userAgent, 'iPad') !== false) {
            $deviceInfo['device'] = 'Tablet';
        } else {
            $deviceInfo['device'] = 'Desktop';
        }

        return $deviceInfo;
    }
}

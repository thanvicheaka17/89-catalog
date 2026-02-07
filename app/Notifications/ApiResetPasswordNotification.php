<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\SiteSetting;
use App\Services\TokenEncryptionService;

class ApiResetPasswordNotification extends Notification
{
    use Queueable;

    protected string $token;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $frontendUrl = SiteSetting::where('key', 'frontend_url')->first()->value
            ?? config('app.frontend_url')
            ?? config('app.url');

        // Encrypt the token before putting it in the URL
        $encryptionService = app(TokenEncryptionService::class);
        $encryptedToken = $encryptionService->encryptForUrl($this->token);

        $url = $frontendUrl . "/reset-password?token={$encryptedToken}&email={$notifiable->email}";

        return (new MailMessage)
            ->subject('Reset Password')
            ->action('Reset Password', $url);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}

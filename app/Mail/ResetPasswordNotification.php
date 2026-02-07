<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Attachment;
use App\Services\TokenEncryptionService;
use App\Models\SiteSetting;
class ResetPasswordNotification extends Mailable
{
    use Queueable, SerializesModels;

    protected string $token;
    protected string $email;
    protected string $name;
    /**
     * Create a new mailable instance.
     */
    public function __construct(string $token, string $email, string $name)
    {
        $this->token = $token;
        $this->email = $email;
        $this->name = $name;
    }

    /**
     * Get the mail envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reset Password - ' . SiteSetting::get('site_name', 'CLICKENGINE'),
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            to: [new Address($this->email)],
        );
    }

    /**
     * Get the mail content.
     */
    public function content(): Content
    {
        // Encrypt the token before sending it in the email
        $encryptionService = app(TokenEncryptionService::class);
        $encryptedToken = $encryptionService->encryptForUrl($this->token);

        return new Content(
            view: 'emails.reset-password',
            with: [
                'token' => $encryptedToken,
                'email' => $this->email,
                'name' => $this->name,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}

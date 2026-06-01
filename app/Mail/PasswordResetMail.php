<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable {
    use Queueable, SerializesModels;

    public function __construct(public readonly string $url, public readonly int $expiresAfter) {}

    public function envelope(): Envelope {
        return new Envelope(
            subject: 'Password Reset Code',
        );
    }

    public function content(): Content {
        return new Content(
            view: 'mail.password-reset.password-reset-mail',
            with: [
                'url' => $this->url,
                'expiresAfter' => $this->expiresAfter,
            ],
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array {
        return [];
    }
}

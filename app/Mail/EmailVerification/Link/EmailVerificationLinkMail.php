<?php

namespace App\Mail\EmailVerification\Link;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmailVerificationLinkMail extends Mailable {
    use Queueable, SerializesModels;

    public function __construct(public readonly string $url, public readonly int $expiresAfter) {}

    public function envelope(): Envelope {
        return new Envelope(
            subject: 'Verify Your Email Address',
        );
    }

    public function content(): Content {
        return new Content(
            view: 'mail.email-verification.link-email-verification-mail',
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

<?php

namespace App\Mail\EmailVerification;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmailVerificationMail extends Mailable implements ShouldQueue {
    use Queueable, SerializesModels;

    public function __construct(public readonly string $url) {}

    public function envelope(): Envelope {
        return new Envelope(
            subject: 'Email Verification',
        );
    }

    public function content(): Content {
        return new Content(
            view: 'mail.email-verification-mail',
            with: [
                'url' => $this->url,
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

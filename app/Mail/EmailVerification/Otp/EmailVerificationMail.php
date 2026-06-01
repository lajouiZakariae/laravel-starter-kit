<?php

namespace App\Mail\EmailVerification\Otp;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmailVerificationMail extends Mailable {
    use Queueable, SerializesModels;

    public function __construct(public readonly string $otpCode) {}

    public function envelope(): Envelope {
        return new Envelope(
            subject: 'Email Verification Mail',
        );
    }

    public function content(): Content {
        return new Content(
            view: 'mail.email-verification-mail',
            with: [
                'otpCode' => $this->otpCode,
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

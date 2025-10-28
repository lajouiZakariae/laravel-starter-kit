<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpPasswordResetMail extends Mailable {
    use Queueable, SerializesModels;

    public function __construct(public readonly string $otpCode) {}

    public function envelope(): Envelope {
        return new Envelope(
            subject: 'Password Reset Code',
        );
    }

    public function content(): Content {
        return new Content(
            view: 'mail.otp-password-reset-mail',
            with: [
                'otpCode' => $this->otpCode,
            ],
        );
    }

    /**
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array {
        return [];
    }
}

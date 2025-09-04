<?php

namespace App\Mail;

use App\Data\Mail\UserMailData;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserRegisteredMail extends Mailable {
    use Queueable, SerializesModels;

    public function __construct(public readonly UserMailData $user) {}

    public function envelope(): Envelope {
        return new Envelope(
            subject: 'User Registered Mail',
        );
    }

    public function content(): Content {
        return new Content(
            view: 'mail.user-registered-mail',
        );
    }

    /** @return array<int, \Illuminate\Mail\Mailables\Attachment> */
    public function attachments(): array {
        return [];
    }
}

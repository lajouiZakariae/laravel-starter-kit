<?php

namespace App\Services;

use App\Data\Mail\UserMailData;
use App\Mail\EmailVerificationMail;
use App\Mail\OtpPasswordResetMail;
use App\Mail\UserRegisteredMail;
use Illuminate\Contracts\Mail\Mailer;

class UserMailerService {
    public function __construct(private readonly Mailer $mailer) {}

    public function sendUserRegisteredEmailForUser(UserMailData $userMailData): void {
        $this->mailer->to($userMailData->email)->send(new UserRegisteredMail($userMailData));
    }

    public function sendVerificationEmail(string $email, string $otpCode): void {
        $this->mailer->to($email)->send(new EmailVerificationMail($otpCode));
    }

    public function sendPasswordResetEmail(string $email, string $otpCode, int $expire): void {
        $this->mailer->to($email)->send(new OtpPasswordResetMail($otpCode, $expire));
    }
}

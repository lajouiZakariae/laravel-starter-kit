<?php

namespace App\Services;

use App\Data\Mail\UserMailData;
use App\Mail\EmailVerification\Otp\EmailVerificationMail;
use App\Mail\OtpPasswordResetMail;
use App\Mail\UserRegisteredMail;
use Illuminate\Contracts\Mail\Mailer;

class UserMailerService {
    public function __construct(private readonly Mailer $mailer) {}

    public function sendUserRegisteredEmailForUser(UserMailData $userMailData): void {
        $mail = (new UserRegisteredMail($userMailData))->onQueue('user-emails-queue');

        $this->mailer->to($userMailData->email)->queue($mail);
    }

    public function sendVerificationEmail(string $email, string $otpCode): void {
        $mail = (new EmailVerificationMail($otpCode))->onQueue('user-emails-queue');

        $this->mailer->to($email)->queue($mail);
    }

    public function sendPasswordResetEmail(string $email, string $otpCode, int $expire): void {
        $mail = (new OtpPasswordResetMail($otpCode, $expire))->onQueue('user-emails-queue');

        $this->mailer->to($email)->queue($mail);
    }
}

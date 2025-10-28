<?php

namespace App\Services;

use App\Data\Mail\UserMailData;
use App\Mail\EmailVerificationMail;
use App\Mail\OtpPasswordResetMail;
use App\Mail\UserRegisteredMail;
use Illuminate\Support\Facades\Mail;

class UserMailerService {
    public function __construct() {}

    public function sendUserRegisteredEmailForUser(UserMailData $userMailData): void {
        Mail::to($userMailData->email)->send(new UserRegisteredMail($userMailData));
    }

    public function sendVerificationEmail(string $email, string $otpCode): void {
        Mail::to($email)->send(new EmailVerificationMail($otpCode));
    }

    public function sendPasswordResetEmail(string $email, string $otpCode): void {
        Mail::to($email)->send(new OtpPasswordResetMail($otpCode));
    }
}

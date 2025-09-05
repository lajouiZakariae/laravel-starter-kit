<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Validation\ValidationException;

class EmailVerificationService {
    public function __construct(
        private readonly UserMailerService $userMailerService,
        private readonly OtpCacheService $otpCacheService,
    ) {}

    public function sendVerificationEmail(User $user): void {
        $otpCode = CodeGeneratorService::generate();

        $this->otpCacheService->cacheOtpCodeForUser($user, $otpCode);

        $this->userMailerService->sendVerificationEmail($user->email, $otpCode);
    }

    public function verifyEmail(User $user, string $otpCode): void {
        $otpCodeFromCache = $this->otpCacheService->getOtpCodeForUser($user);

        $isOtpCodeInvalid = $otpCodeFromCache !== $otpCode;

        if ($isOtpCodeInvalid) {
            throw ValidationException::withMessages([
                'otp_code' => ['Invalid OTP code provided'],
            ]);
        }

        $this->otpCacheService->deleteOtpCodeForUser($user);

        $user->markEmailAsVerified();
    }
}

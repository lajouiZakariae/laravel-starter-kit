<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class PasswordResetService {
    public function __construct(
        private readonly UserMailerService $userMailerService,
        private readonly OtpCacheService $otpCacheService,
    ) {}

    public function sendPasswordResetEmail(User $user): void {
        $otpCode = CodeGeneratorService::generate();

        $this->otpCacheService->cacheOtpCodeForUser($user, $otpCode);

        $this->userMailerService->sendPasswordResetEmail($user->email, $otpCode);
    }

    public function verifyPasswordResetCode(User $user, string $otpCode): void {
        $otpCodeFromCache = $this->otpCacheService->getOtpCodeForUser($user);

        $isOtpCodeInvalid = $otpCodeFromCache !== $otpCode;

        if ($isOtpCodeInvalid) {
            throw ValidationException::withMessages([
                'otp_code' => ['Invalid OTP code provided'],
            ]);
        }
    }

    public function resetPassword(User $user, string $otpCode, string $newPassword): void {
        $otpCodeFromCache = $this->otpCacheService->getOtpCodeForUser($user);

        $isOtpCodeInvalid = $otpCodeFromCache !== $otpCode;

        if ($isOtpCodeInvalid) {
            throw ValidationException::withMessages([
                'otp_code' => ['Invalid OTP code provided'],
            ]);
        }

        $this->otpCacheService->deleteOtpCodeForUser($user);

        $user->update([
            'password' => Hash::make($newPassword),
        ]);
    }
}

<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Services\OtpCacheService;
use App\Services\UserMailerService;
use CodeGeneratorService;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Validation\ValidationException;

class OtpPasswordResetService {
    public function __construct(
        private readonly UserMailerService $userMailerService,
        private readonly OtpCacheService $otpCacheService,
        private readonly Hasher $hasher,
        private readonly Repository $configRepository,
    ) {}

    public function sendOtpPasswordResetEmail(User $user): void {
        $otpCode = CodeGeneratorService::generate();

        $expire = $this->configRepository->get('auth.passwords.' . $this->configRepository->get('auth.defaults.passwords') . '.expire');

        $ttl = now()->addMinutes($expire);

        $this->otpCacheService->cacheOtpCodeForUser($user, $otpCode, $ttl);

        $this->userMailerService->sendPasswordResetEmail($user->email, $otpCode, $expire);
    }

    public function verifyOtpPasswordResetCode(User $user, string $otpCode): void {
        $otpCodeFromCache = $this->otpCacheService->getOtpCodeForUser($user);

        $isOtpCodeInvalid = $otpCodeFromCache !== $otpCode;

        if ($isOtpCodeInvalid) {
            throw ValidationException::withMessages([
                'otp_code' => ['Invalid OTP code provided'],
            ]);
        }
    }

    public function resetOtpPassword(User $user, string $otpCode, string $newPassword): void {
        $otpCodeFromCache = $this->otpCacheService->getOtpCodeForUser($user);

        $isOtpCodeInvalid = $otpCodeFromCache !== $otpCode;

        if ($isOtpCodeInvalid) {
            throw ValidationException::withMessages([
                'otp_code' => ['Invalid OTP code provided'],
            ]);
        }

        $this->otpCacheService->deleteOtpCodeForUser($user);

        $user->update([
            'password' => $this->hasher->make($newPassword),
        ]);
    }
}

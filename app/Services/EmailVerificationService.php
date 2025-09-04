<?php

namespace App\Services;

use App\Models\User;

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
}

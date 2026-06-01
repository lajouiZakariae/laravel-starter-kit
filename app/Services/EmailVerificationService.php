<?php

namespace App\Services;

use App\Data\Mail\EmailVerificationLinkMailData;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Config\Repository;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class EmailVerificationService {
    public function __construct(
        private readonly UserMailerService $userMailerService,
        private readonly OtpCacheService $otpCacheService,
        private readonly Repository $configRepository,
        private readonly ConnectionInterface $db,
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

    public function sendVerificationLink(string $email): void {
        $plainToken = Str::random(64);

        $hashedToken = hash('sha256', $plainToken);

        $this->db->table('email_verification_tokens')->upsert(
            [
                'email' => $email,
                'token' => $hashedToken,
                'created_at' => now(),
            ],
            ['email'],
            ['token', 'created_at'],
        );

        $ttl = $this->configRepository->integer('jwt_auth.email_verification_token_ttl');

        $url = url(route('api.auth.email.link.verify', [], false)) . '?' . http_build_query([
            'email' => $email,
            'token' => $plainToken,
        ]);

        $data = new EmailVerificationLinkMailData(
            email: $email,
            url: $url,
            expiresAfter: $ttl,
        );

        $this->userMailerService->sendEmailVerificationLink($data);
    }

    public function verifyEmailWithLink(string $email, string $token): void {
        $record = $this->db->table('email_verification_tokens')
            ->where('email', $email)
            ->first();

        if ($record === null || ! hash_equals($record->token, hash('sha256', $token))) {
            throw new BadRequestHttpException('Invalid email verification token');
        }

        $ttl = $this->configRepository->integer('jwt_auth.email_verification_token_ttl');

        $isExpired = now()->isAfter(Carbon::parse($record->created_at)->addMinutes($ttl));

        if ($isExpired) {
            throw new BadRequestHttpException('Email verification token has expired');
        }

        $user = User::query()->where('email', $email)->first();

        if ($user === null) {
            throw new BadRequestHttpException('Invalid email verification token');
        }

        $this->db->table('email_verification_tokens')->where('email', $email)->delete();

        $user->markEmailAsVerified();
    }
}

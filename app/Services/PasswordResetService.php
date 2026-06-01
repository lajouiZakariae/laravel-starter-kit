<?php

namespace App\Services;

use App\Data\ResetPasswordData;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Passwords\PasswordBrokerManager;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class PasswordResetService {
    public function __construct(
        private readonly UserMailerService $userMailerService,
        private readonly OtpCacheService $otpCacheService,
        private readonly PasswordBrokerManager $passwordBrokerManager,
        private readonly Hasher $hasher,
        private readonly Dispatcher $dispatcher,
        private readonly Repository $configRepository,
    ) {}

    public function sendPasswordResetEmail(string $email): void {
        $status = $this->passwordBrokerManager->sendResetLink(['email' => $email]);

        if ($status !== Password::ResetLinkSent) {
            throw new BadRequestHttpException('Password reset code could not be sent');
        }
    }

    public function resetPassword(ResetPasswordData $data): void {
        $status = $this->passwordBrokerManager->reset($data->toArray(), function (User $user, string $password): void {
            $user->forceFill([
                'password' => $this->hasher->make($password),
            ]);

            $user->save();

            $this->dispatcher->dispatch(new PasswordReset($user));
        });

        if ($status !== Password::PasswordReset) {
            throw new BadRequestHttpException('Password could not be reset');
        }
    }

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

<?php

namespace App\Services;

use App\Data\ResetPasswordData;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class PasswordResetService {
    public function __construct(
        private readonly UserMailerService $userMailerService,
        private readonly OtpCacheService $otpCacheService,
    ) {}

    public function sendPasswordResetEmail(string $email): void {
        $status = Password::sendResetLink(['email' => $email]);

        if ($status !== Password::ResetLinkSent) {
            throw new BadRequestHttpException('Password reset code could not be sent');
        }
    }

    public function resetPassword(ResetPasswordData $data): void {
        $status = Password::reset($data->toArray(), function (User $user, string $password): void {
            $user->forceFill([
                'password' => Hash::make($password),
            ]);

            $user->save();

            event(new PasswordReset($user));
        });

        if ($status !== Password::PasswordReset) {
            throw new BadRequestHttpException('Password could not be reset');
        }
    }

    public function sendOtpPasswordResetEmail(User $user): void {
        $otpCode = CodeGeneratorService::generate();

        $this->otpCacheService->cacheOtpCodeForUser($user, $otpCode);

        $this->userMailerService->sendPasswordResetEmail($user->email, $otpCode);
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
            'password' => Hash::make($newPassword),
        ]);
    }
}

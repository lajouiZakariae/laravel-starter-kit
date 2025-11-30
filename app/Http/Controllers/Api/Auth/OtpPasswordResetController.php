<?php

namespace App\Http\Controllers\Api\Auth;

use App\Concerns\ApiResponse;
use App\Http\Requests\Api\OtpResetPasswordRequest;
use App\Http\Requests\Api\SendPasswordResetRequest;
use App\Http\Requests\Api\VerifyPasswordResetRequest;
use App\Models\User;
use App\Services\PasswordResetService;
use Illuminate\Http\JsonResponse;

class OtpPasswordResetController {
    use ApiResponse;

    public function __construct(
        private readonly PasswordResetService $passwordResetService,
    ) {}

    public function sendPasswordResetCode(SendPasswordResetRequest $request): JsonResponse {
        $user = User::query()->where('email', $request->string('email'))->firstOrFail();

        $this->passwordResetService->sendOtpPasswordResetEmail($user);

        return $this->successResponse(['message' => 'Password reset code sent']);
    }

    public function verifyPasswordResetCode(VerifyPasswordResetRequest $request): JsonResponse {
        $user = User::query()->where('email', $request->string('email'))->firstOrFail();

        $this->passwordResetService->verifyOtpPasswordResetCode($user, $request->string('otp_code'));

        return $this->successResponse(['message' => 'Password reset code is valid']);
    }

    public function resetPassword(OtpResetPasswordRequest $request): JsonResponse {
        $user = User::query()->where('email', $request->string('email'))->firstOrFail();

        $this->passwordResetService->resetOtpPassword(
            $user,
            $request->string('otp_code'),
            $request->string('password')
        );

        return $this->successResponse(['message' => 'Password reset successfully']);
    }
}

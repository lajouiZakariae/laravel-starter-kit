<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\SendPasswordResetRequest;
use App\Http\Requests\VerifyPasswordResetRequest;
use App\Models\User;
use App\Services\PasswordResetService;
use Illuminate\Http\JsonResponse;

/**
 * @tags Password Reset
 */
class PasswordResetController {
    public function __construct(
        private readonly PasswordResetService $passwordResetService,
    ) {}

    /**
     * Send password reset code
     */
    public function sendPasswordResetCode(SendPasswordResetRequest $request): JsonResponse {
        $user = User::where('email', $request->email)->firstOrFail();

        $this->passwordResetService->sendPasswordResetEmail($user);

        return response()->json(['message' => 'Password reset code sent']);
    }

    /**
     * Verify password reset code
     */
    public function verifyPasswordResetCode(VerifyPasswordResetRequest $request): JsonResponse {
        $user = User::where('email', $request->email)->firstOrFail();

        $this->passwordResetService->verifyPasswordResetCode($user, $request->string('otp_code'));

        return response()->json(['message' => 'Password reset code is valid']);
    }

    /**
     * Reset password
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse {
        $user = User::where('email', $request->email)->firstOrFail();

        $this->passwordResetService->resetPassword(
            $user,
            $request->string('otp_code'),
            $request->string('password')
        );

        return response()->json(['message' => 'Password reset successfully']);
    }
}

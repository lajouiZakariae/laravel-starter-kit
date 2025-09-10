<?php

namespace App\Http\Controllers\Api\Auth;

use App\Data\ResetPasswordData;
use App\Http\Requests\Api\ResetPasswordRequest;
use App\Http\Requests\Api\SendPasswordResetRequest;
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
        $this->passwordResetService->sendPasswordResetEmail($request->string('email'));

        return response()->json(['message' => 'Password reset code sent']);
    }

    /**
     * Reset password
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse {
        $resetPasswordData = ResetPasswordData::from($request);

        $this->passwordResetService->resetPassword($resetPasswordData);

        return response()->json(['message' => 'Password reset']);
    }
}

<?php

namespace App\Http\Controllers\Api\Auth\LinkBased;

use App\Contracts\Http\ApiResponse;
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
        private readonly ApiResponse $apiResponse,
    ) {}

    /**
     * Send password reset code
     */
    public function sendPasswordResetCode(SendPasswordResetRequest $request): JsonResponse {
        $this->passwordResetService->sendPasswordResetEmail($request->string('email'));

        return $this->apiResponse->successResponse(['message' => 'Password reset code sent']);
    }

    /**
     * Reset password
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse {
        $resetPasswordData = ResetPasswordData::from($request);

        $this->passwordResetService->resetPassword($resetPasswordData);

        return $this->apiResponse->successResponse(['message' => 'Password reset']);
    }
}

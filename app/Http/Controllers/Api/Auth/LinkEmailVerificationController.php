<?php

namespace App\Http\Controllers\Api\Auth;

use App\Contracts\Http\ApiResponse;
use App\Http\Requests\Api\SendLinkEmailVerificationRequest;
use App\Http\Requests\Api\VerifyLinkEmailRequest;
use App\Services\EmailVerificationService;
use Illuminate\Http\JsonResponse;

/**
 * @tags Email Verification
 */
class LinkEmailVerificationController {
    public function __construct(
        private readonly EmailVerificationService $emailVerificationService,
        private readonly ApiResponse $apiResponse,
    ) {}

    /**
     * Send verification link
     */
    public function sendVerificationLink(SendLinkEmailVerificationRequest $request): JsonResponse {
        $this->emailVerificationService->sendVerificationLink($request->string('email')->toString());

        return $this->apiResponse->successResponse(['message' => 'Verification link sent']);
    }

    /**
     * Verify email via link
     */
    public function verifyEmail(VerifyLinkEmailRequest $request): JsonResponse {
        $this->emailVerificationService->verifyEmailWithLink(
            $request->string('email')->toString(),
            $request->string('token')->toString(),
        );

        return $this->apiResponse->successResponse(['message' => 'Email verified successfully']);
    }
}

<?php

namespace App\Http\Controllers\Api\Auth\LinkBased;

use App\Contracts\Http\ApiResponse;
use App\Http\Requests\Api\SendLinkEmailVerificationRequest;
use App\Http\Requests\Api\VerifyLinkEmailRequest;
use App\Models\User;
use App\Services\Auth\LinkBasedEmailVerificationService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @tags Email Verification
 */
class EmailVerificationController {
    public function __construct(
        private readonly LinkBasedEmailVerificationService $emailVerificationService,
        private readonly ApiResponse $apiResponse,
    ) {}

    /**
     * Send verification link
     */
    public function sendVerificationLink(SendLinkEmailVerificationRequest $request): JsonResponse {
        $user = User::whereEmail($request->string('email')->toString())->firstOrFail();

        $this->ensureEmailIsNotVerified($user);

        $this->emailVerificationService->sendVerificationLink($request->string('email')->toString());

        return $this->apiResponse->successResponse(['message' => 'Verification link sent']);
    }

    /**
     * Verify email via link
     */
    public function verifyEmailLink(VerifyLinkEmailRequest $request): JsonResponse {
        $this->emailVerificationService->verifyEmailWithLink(
            $request->string('email')->toString(),
            $request->string('token')->toString(),
        );

        return $this->apiResponse->successResponse(['message' => 'Email verified successfully']);
    }

    private function ensureEmailIsNotVerified(User $user): void {
        if ($user->hasVerifiedEmail()) {
            throw new BadRequestHttpException('Email already verified');
        }
    }
}

<?php

namespace App\Http\Controllers\Api\Auth;

use App\Contracts\Http\ApiResponse;
use App\Contracts\UserContext;
use App\Http\Requests\Api\SendLinkEmailVerificationRequest;
use App\Http\Requests\Api\VerifyEmailRequest;
use App\Http\Requests\Api\VerifyLinkEmailRequest;
use App\Models\User;
use App\Services\EmailVerificationService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @tags Email Verification
 */
class EmailVerificationController {
    public function __construct(
        private readonly UserContext $userContext,
        private readonly EmailVerificationService $emailVerificationService,
        private readonly ApiResponse $apiResponse,
    ) {}

    /**
     * Send verification email
     */
    public function sendVerificationEmail(): JsonResponse {
        $authUser = $this->userContext->getAuthenticatedUserOrFail();

        $this->ensureEmailIsNotVerified($authUser);

        $this->emailVerificationService->sendVerificationEmail($authUser);

        return $this->apiResponse->successResponse(['message' => 'Verification email sent']);
    }

    /**
     * Verify email
     */
    public function verifyEmail(VerifyEmailRequest $request): JsonResponse {
        $authUser = $this->userContext->getAuthenticatedUserOrFail();

        $this->ensureEmailIsNotVerified($authUser);

        $this->emailVerificationService->verifyEmail($authUser, $request->string('otp_code'));

        return $this->apiResponse->successResponse(['message' => 'Email verified successfully']);
    }

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

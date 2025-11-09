<?php

namespace App\Http\Controllers\Api\Auth;

use App\Contracts\UserContextInterface;
use App\Http\Requests\Api\VerifyEmailRequest;
use App\Models\User;
use App\Services\EmailVerificationService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @tags Email Verification
 */
class EmailVerificationController {
    public function __construct(
        private readonly UserContextInterface $userContext,
        private readonly EmailVerificationService $emailVerificationService,
    ) {}

    /**
     * Send verification email
     */
    public function sendVerificationEmail(): JsonResponse {
        $authUser = $this->userContext->getAuthenticatedUserOrFail();

        $this->ensureEmailIsNotVerified($authUser);

        $this->emailVerificationService->sendVerificationEmail($authUser);

        return response()->json(['message' => 'Verification email sent']);
    }

    /**
     * Verify email
     */
    public function verifyEmail(VerifyEmailRequest $request): JsonResponse {
        $authUser = $this->userContext->getAuthenticatedUserOrFail();

        $this->ensureEmailIsNotVerified($authUser);

        $this->emailVerificationService->verifyEmail($authUser, $request->string('otp_code'));

        return response()->json(['message' => 'Email verified successfully']);
    }

    private function ensureEmailIsNotVerified(User $user): void {
        if ($user->hasVerifiedEmail()) {
            throw new BadRequestHttpException('Email already verified');
        }
    }
}

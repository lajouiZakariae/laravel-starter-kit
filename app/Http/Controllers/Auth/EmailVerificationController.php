<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\EmailVerificationRequest;
use App\Http\Requests\VerifyEmailRequest;
use App\Interfaces\UserContextInterface;
use App\Models\User;
use App\Services\EmailVerificationService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class EmailVerificationController {
    public function __construct(
        private readonly UserContextInterface $userContext,
        private readonly EmailVerificationService $emailVerificationService,
    ) {}

    public function sendVerificationEmail(EmailVerificationRequest $request): JsonResponse {
        $authUser = $this->userContext->getAuthenticatedUser();

        $user = $authUser ?? User::where('email', $request->email)->firstOrFail();

        $this->ensureEmailIsNotVerified($user);

        $this->emailVerificationService->sendVerificationEmail($user);

        return response()->json(['message' => 'Verification email sent']);
    }

    public function verifyEmail(VerifyEmailRequest $request): JsonResponse {
        $authUser = $this->userContext->getAuthenticatedUser();

        $user = $authUser ?? User::where('email', $request->email)->firstOrFail();

        $this->ensureEmailIsNotVerified($user);

        $this->emailVerificationService->verifyEmail($user, $request->string('otp_code'));

        return response()->json(['message' => 'Email verified successfully']);
    }

    private function ensureEmailIsNotVerified(User $user): void {
        if ($user->hasVerifiedEmail()) {
            throw new BadRequestHttpException('Email already verified');
        }
    }
}

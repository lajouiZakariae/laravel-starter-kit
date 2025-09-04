<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmailVerificationRequest;
use App\Interfaces\UserContextInterface;
use App\Models\User;
use App\Services\EmailVerificationService;
use Illuminate\Http\JsonResponse;

class EmailVerificationController {
    public function __construct(
        private readonly UserContextInterface $userContext,
        private readonly EmailVerificationService $emailVerificationService,
    ) {}

    public function sendVerificationEmail(EmailVerificationRequest $request): JsonResponse {
        $authUser = $this->userContext->getAuthenticatedUser();

        $user = $authUser ?? User::where('email', $request->email)->firstOrFail();

        $this->emailVerificationService->sendVerificationEmail($user);

        return response()->json(['message' => 'Verification email sent']);
    }
}

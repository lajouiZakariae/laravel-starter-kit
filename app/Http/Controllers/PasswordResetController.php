<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\SendPasswordResetRequest;
use App\Http\Requests\VerifyPasswordResetRequest;
use App\Models\User;
use App\Services\PasswordResetService;
use Illuminate\Http\JsonResponse;

class PasswordResetController {
    public function __construct(
        private readonly PasswordResetService $passwordResetService,
    ) {}

    public function sendPasswordResetCode(SendPasswordResetRequest $request): JsonResponse {
        $user = User::where('email', $request->email)->firstOrFail();

        $this->passwordResetService->sendPasswordResetEmail($user);

        return response()->json(['message' => 'Password reset code sent']);
    }

    public function verifyPasswordResetCode(VerifyPasswordResetRequest $request): JsonResponse {
        $user = User::where('email', $request->email)->firstOrFail();

        $this->passwordResetService->verifyPasswordResetCode($user, $request->string('otp_code'));

        return response()->json(['message' => 'Password reset code is valid']);
    }

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

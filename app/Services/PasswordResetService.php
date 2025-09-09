<?php

namespace App\Services;

use App\Data\ResetPasswordData;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class PasswordResetService {
    public function __construct() {}

    public function sendPasswordResetEmail(string $email): void {
        $status = Password::sendResetLink(['email' => $email]);

        if ($status !== Password::ResetLinkSent) {
            throw new BadRequestHttpException('Password reset code could not be sent');
        }
    }

    public function resetPassword(ResetPasswordData $data): void {
        $status = Password::reset($data->toArray(), function (User $user, string $password): void {
            $user->forceFill([
                'password' => Hash::make($password),
            ]);

            $user->save();

            event(new PasswordReset($user));
        });

        if ($status !== Password::PasswordReset) {
            throw new BadRequestHttpException('Password could not be reset');
        }
    }
}

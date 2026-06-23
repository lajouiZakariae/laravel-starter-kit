<?php

namespace App\Services\Auth;

use App\Data\ResetPasswordData;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Passwords\PasswordBrokerManager;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Password;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class LinkBasedPasswordResetService {
    public function __construct(
        private readonly PasswordBrokerManager $passwordBrokerManager,
        private readonly Hasher $hasher,
        private readonly Dispatcher $dispatcher,
    ) {}

    public function sendPasswordResetEmail(string $email): void {
        $status = $this->passwordBrokerManager->sendResetLink(['email' => $email]);

        if ($status !== Password::ResetLinkSent) {
            throw new BadRequestHttpException('Password reset code could not be sent');
        }
    }

    public function resetPassword(ResetPasswordData $data): void {
        $status = $this->passwordBrokerManager->reset($data->toArray(), function (User $user, string $password): void {
            $user->forceFill([
                'password' => $this->hasher->make($password),
            ]);

            $user->save();

            $this->dispatcher->dispatch(new PasswordReset($user));
        });

        if ($status !== Password::PasswordReset) {
            throw new BadRequestHttpException('Password could not be reset');
        }
    }
}

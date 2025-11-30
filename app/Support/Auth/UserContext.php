<?php

namespace App\Support\Auth;

use App\Contracts\UserContext as UserContextContract;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Guard;

class UserContext implements UserContextContract {
    public function __construct(private readonly Guard $guard) {}

    public function getAuthenticatedUser(): ?User {
        return $this->guard->user();
    }

    public function getAuthenticatedUserOrFail(): User {
        return $this->guard->user() ?? throw new AuthenticationException;
    }
}

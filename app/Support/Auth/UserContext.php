<?php

namespace App\Support\Auth;

use App\Contracts\UserContext as UserContextContract;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;

class UserContext implements UserContextContract {
    public function getAuthenticatedUser(): ?User {
        return auth()->user();
    }

    public function getAuthenticatedUserOrFail(): User {
        return auth()->user() ?? throw new AuthenticationException;
    }
}

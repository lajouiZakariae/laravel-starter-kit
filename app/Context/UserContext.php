<?php

namespace App\Context;

use App\Interfaces\UserContextInterface;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;

class UserContext implements UserContextInterface {
    public function getAuthenticatedUser(): ?User {
        return auth()->user();
    }

    public function getAuthenticatedUserOrFail(): User {
        return auth()->user() ?? throw new AuthenticationException;
    }
}

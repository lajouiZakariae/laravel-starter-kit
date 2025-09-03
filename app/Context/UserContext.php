<?php

namespace App\Context;

use App\Interfaces\UserContextInterface;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;

class UserContext implements UserContextInterface {
    public function getAuthenticatedUser(): User {
        $authUser = auth()->user();

        if (! $authUser) {
            throw new AuthenticationException;
        }

        return $authUser;
    }
}

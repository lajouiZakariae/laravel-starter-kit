<?php

namespace App\Services;

use App\Models\User;

class UserService {
    public function loadRelations(User $user): User {
        $user->load('country');

        return $user;
    }
}

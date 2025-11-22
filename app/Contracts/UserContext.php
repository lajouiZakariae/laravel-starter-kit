<?php

namespace App\Contracts;

use App\Models\User;

interface UserContext {
    public function getAuthenticatedUser(): ?User;

    public function getAuthenticatedUserOrFail(): User;
}

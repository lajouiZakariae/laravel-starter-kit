<?php

namespace App\Contracts;

use App\Models\User;

interface UserContextInterface {
    public function getAuthenticatedUser(): ?User;

    public function getAuthenticatedUserOrFail(): User;
}

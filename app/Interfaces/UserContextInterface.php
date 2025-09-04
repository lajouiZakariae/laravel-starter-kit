<?php

namespace App\Interfaces;

use App\Models\User;

interface UserContextInterface {
    public function getAuthenticatedUser(): ?User;

    public function getAuthenticatedUserOrFail(): User;
}

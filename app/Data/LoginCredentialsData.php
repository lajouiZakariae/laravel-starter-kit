<?php

namespace App\Data;

use App\Enums\Role\UserRoleEnum;
use Spatie\LaravelData\Data;

class LoginCredentialsData extends Data {
    public function __construct(
        public string $email,
        public string $password,
        public UserRoleEnum $signed_in_as,
    ) {}
}

<?php

namespace App\Data;

use App\Models\User;
use Spatie\LaravelData\Data;

class AuthResultData extends Data {
    public function __construct(
        public User $user,
        public string $token,
        public string $tokenType = 'Bearer',
    ) {}
}

<?php

namespace App\Data;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
class RegisterUserData extends Data {
    public function __construct(
        public string $first_name,
        public string $last_name,
        public string $email,
        public string $password,
    ) {}
}

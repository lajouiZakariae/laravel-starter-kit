<?php

namespace App\Data;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
class ResetPasswordData extends Data {
    public function __construct(
        public string $email,
        public string $token,
        public string $password,
    ) {}
}

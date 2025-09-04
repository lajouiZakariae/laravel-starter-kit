<?php

namespace App\Data\Mail;

use Carbon\Carbon;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
class UserMailData extends Data {
    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $email,
        public Carbon $createdAt,
    ) {}
}

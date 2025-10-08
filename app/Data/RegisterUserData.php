<?php

namespace App\Data;

use App\Data\Casts\PhoneNumberDataCast;
use App\ValueObjects\PhoneNumber;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
class RegisterUserData extends Data {
    public function __construct(
        public string $first_name,
        public string $last_name,
        public string $email,
        public string $password,
        public string $phone_number_country_code,
        #[WithCast(PhoneNumberDataCast::class)]
        public PhoneNumber $phone_number,
    ) {}
}

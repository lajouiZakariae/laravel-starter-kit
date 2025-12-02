<?php

namespace App\Data\city;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class CreateCityData extends Data {
    public function __construct(
        public string $name,
        public bool $isActive = true,
    ) {}
}

<?php

namespace App\Data\Country;

use Spatie\LaravelData\Data;

class TranslatedPropertyData extends Data {
    public function __construct(
        public ?string $en = null,
        public ?string $fr = null,
        public ?string $ar = null,
    ) {}
}

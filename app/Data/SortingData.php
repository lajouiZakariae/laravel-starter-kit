<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class SortingData extends Data {
    public function __construct(
        public readonly ?string $sortBy,
        public readonly ?string $order,
    ) {}
}

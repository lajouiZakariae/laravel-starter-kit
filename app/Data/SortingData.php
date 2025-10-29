<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class SortingData extends Data {
    public function __construct(
        public readonly string $sortBy = 'created_at',
        public readonly string $order = 'desc',
    ) {}
}

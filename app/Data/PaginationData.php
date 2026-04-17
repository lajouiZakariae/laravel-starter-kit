<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class PaginationData extends Data {
    public function __construct(
        public readonly bool $paginate = true,
        public readonly int $perPage = 10,
    ) {}
}

<?php

namespace App\Services;

use Illuminate\Support\Collection;

class CodeGeneratorService {
    public static function generate(int $length = 6): string {
        return (new Collection(range(0, $length - 1)))->map(fn (): int => random_int(0, 9))->join('');
    }
}

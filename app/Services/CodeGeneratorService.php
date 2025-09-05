<?php

namespace App\Services;

class CodeGeneratorService {
    public static function generate(int $length = 6): string {
        return collect(range(0, $length - 1))->map(fn (): int => random_int(0, 9))->join('');
    }
}

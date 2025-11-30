<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidOtpFormatRule implements ValidationRule {
    public function __construct(private readonly Repository $repository) {}

    /**
     * @param  Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        if (! is_string($value)) {
            $fail(__('validation.string', ['attribute' => $attribute]));

            return;
        }

        if (mb_strlen($value) !== $this->repository->integer('auth.otp.size')) {
            $errorMessage = __('validation.size.string', ['attribute' => $attribute, 'size' => $this->repository->integer('auth.otp.size')]);

            $fail($errorMessage);
        }

        if (! preg_match('/^[0-9]+$/', $value)) {
            $fail(__('validation.numeric', ['attribute' => $attribute]));
        }
    }
}

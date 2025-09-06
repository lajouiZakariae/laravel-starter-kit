<?php

namespace App\Casts;

use App\ValueObjects\PhoneNumber;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * @implements CastsAttributes<PhoneNumber, string>
 */
class AsPhoneNumber implements CastsAttributes {
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed {
        if (! is_string($value) || empty($value)) {
            return null;
        }

        return new PhoneNumber($value);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed {
        if (! $value instanceof PhoneNumber) {
            throw new \InvalidArgumentException('The value must be an instance of PhoneNumber');
        }

        return $value->phoneNumber;
    }
}

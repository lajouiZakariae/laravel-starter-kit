<?php

namespace App\Casts;

use App\ValueObjects\PhoneNumber;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

/**
 * @implements CastsAttributes<string, PhoneNumber>
 */
class AsPhoneNumber implements CastsAttributes {
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): ?PhoneNumber {
        $countryCode = data_get($attributes, 'phone_number_country_code');

        if (! is_string($countryCode) || empty($countryCode)) {
            throw new InvalidArgumentException('The country code must be a string');
        }

        if (! is_string($value) || empty($value)) {
            throw new InvalidArgumentException('The value must be a string');
        }

        return new PhoneNumber($value, $countryCode);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): array {
        if (! $value instanceof PhoneNumber) {
            throw new InvalidArgumentException('The value must be an instance of PhoneNumber');
        }

        return ['phone_number_country_code' => $value->iso2CountryCode, $key => $value->phoneNumber];
    }
}

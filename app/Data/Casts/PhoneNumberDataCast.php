<?php

namespace App\Data\Casts;

use App\Exceptions\PhoneNumberException;
use App\ValueObjects\PhoneNumber;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

class PhoneNumberDataCast implements Cast {
    public function cast(
        DataProperty $property,
        mixed $value,
        array $properties,
        CreationContext $context
    ): ?PhoneNumber {
        if ($value instanceof PhoneNumber) {
            return $value;
        }

        $countryCode = data_get($properties, 'phone_number_country_code');

        if (! is_string($value) || ! is_string($countryCode)) {
            return null;
        }

        $phoneNumber = trim($value);
        $countryCode = trim($countryCode);

        if (blank($phoneNumber) || blank($countryCode)) {
            return null;
        }

        try {
            return new PhoneNumber($phoneNumber, $countryCode);
        } catch (PhoneNumberException) {
            return null;
        }
    }
}

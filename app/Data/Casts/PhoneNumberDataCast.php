<?php

namespace App\Data\Casts;

use App\ValueObjects\PhoneNumber;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;
use Stringable;

class PhoneNumberDataCast implements Cast {
    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): mixed {
        if ($value instanceof PhoneNumber) {
            return $value;
        }

        if (! is_string($value) && ! $value instanceof Stringable) {
            $value = (string) $value;
        }

        return new PhoneNumber($value);
    }

    public function uncast(DataProperty $property, mixed $value, array $context): string {
        if (! $value instanceof PhoneNumber) {
            return (string) $value;
        }

        return $value->toString();
    }
}

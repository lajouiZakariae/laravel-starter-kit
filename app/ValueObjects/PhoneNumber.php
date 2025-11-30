<?php

namespace App\ValueObjects;

use App\Exceptions\PhoneNumberException;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

class PhoneNumber {
    public readonly string $phoneNumber;

    public readonly string $nationalPhoneNumber;

    public readonly int $numericCountryCode;

    public readonly string $iso2CountryCode;

    public function __construct(string $phoneNumber, ?string $defaultRegion = null) {
        $phoneUtil = PhoneNumberUtil::getInstance();

        try {
            $libPhoneNumber = $phoneUtil->parse($phoneNumber, $defaultRegion);

            if (! $phoneUtil->isValidNumber($libPhoneNumber)) {
                throw new PhoneNumberException("Invalid phone number: {$phoneNumber}");
            }

            $this->phoneNumber = $phoneUtil->format($libPhoneNumber, PhoneNumberFormat::E164);

            $nationalPhoneNumber = $libPhoneNumber->getNationalNumber();

            if (blank($nationalPhoneNumber)) {
                throw new PhoneNumberException("Invalid national phone number: {$phoneNumber}");
            }

            $this->nationalPhoneNumber = $nationalPhoneNumber;

            $numericCountryCode = $libPhoneNumber->getCountryCode();

            if (blank($numericCountryCode)) {
                throw new PhoneNumberException("Invalid numeric country code: {$phoneNumber}");
            }

            $this->numericCountryCode = $numericCountryCode;

            $iso2CountryCode = $phoneUtil->getRegionCodeForNumber($libPhoneNumber);

            if (blank($iso2CountryCode)) {
                throw new PhoneNumberException("Invalid ISO 2 country code: {$phoneNumber}");
            }

            $this->iso2CountryCode = $iso2CountryCode;

        } catch (NumberParseException $e) {
            throw new PhoneNumberException("Unable to parse phone number: {$phoneNumber}. Error: " . $e->getMessage());
        }
    }

    /**
     * Create a PhoneNumber instance with a default region
     */
    public static function make(string $phoneNumber, ?string $defaultRegion = null): self {
        return new self($phoneNumber, $defaultRegion);
    }

    public function toString(): string {
        return $this->phoneNumber;
    }
}

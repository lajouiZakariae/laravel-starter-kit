<?php

namespace App\Http\Requests\Api;

use App\Models\Country;
use App\Models\User;
use App\ValueObjects\PhoneNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Propaganistas\LaravelPhone\Rules\Phone;

abstract class BaseRequest extends FormRequest {
    protected function imageRules(): array {
        return [
            'image', 'mimes:jpg,png,webp', 'max:2048',
        ];
    }

    protected function phoneRules(bool $required = true, bool $optional = false): array {
        return [
            'phone_number_country_code' => [
                $optional ? 'sometimes' : ($required ? 'required' : 'nullable'),
                'string',
                Rule::exists(Country::class, 'iso_3166_1_alpha2')->where('is_active', true),
            ],
            'phone_number' => [
                $optional ? 'sometimes' : ($required ? 'required' : 'nullable'),
                (new Phone)->countryField('phone_number_country_code'),
                Rule::unique(User::class, 'phone_number'),
            ],
        ];
    }

    public function preparePhoneNumberForValidation(
        string $phoneNumberField = 'phone_number',
        string $countryCodeField = 'phone_number_country_code',
    ): void {
        try {
            if (! $this->filled($phoneNumberField) && ! $this->filled($countryCodeField)) {
                return;
            }

            $phoneNumber = new PhoneNumber(
                $this->input($phoneNumberField),
                $this->input($countryCodeField),
            );

            $this->merge([
                $phoneNumberField => $phoneNumber->phoneNumber,
                $countryCodeField => strtoupper($phoneNumber->iso2CountryCode),
            ]);

        } catch (\Exception $e) {
            throw ValidationException::withMessages([
                $phoneNumberField => __('validation.phone', ['attribute' => __('validation.attributes.' . $phoneNumberField)]),
            ]);
        }
    }
}

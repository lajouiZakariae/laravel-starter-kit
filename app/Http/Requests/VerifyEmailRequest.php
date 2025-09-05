<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Rules\ValidOtpLengthRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VerifyEmailRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array {
        $rules = [
            'otp_code' => ['required', 'string', new ValidOtpLengthRule],
        ];

        if (! $this->user()) {
            $rules['email'] = [
                'required',
                'email',
                Rule::exists(User::class, 'email'),
            ];
        }

        return $rules;
    }
}

<?php

namespace App\Http\Requests\Api;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VerifyLinkEmailRequest extends FormRequest {
    public function authorize(): bool {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array {
        return [
            'email' => [
                'required',
                'email',
                Rule::exists(User::class, 'email'),
            ],
            'token' => ['required', 'string'],
        ];
    }
}

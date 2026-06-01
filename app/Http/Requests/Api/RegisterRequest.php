<?php

namespace App\Http\Requests\Api;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

class RegisterRequest extends BaseRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return true;
    }

    public function prepareForValidation(): void {
        $this->preparePhoneNumberForValidation();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class, 'email')],
            'password' => ['required', 'string', 'min:8'],
            ...$this->phoneRules(optional: true),
        ];
    }
}

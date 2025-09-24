<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class LoginRequest
 *
 * Handles validation and authorization logic for user login requests.
 *
 * @package App\Http\Requests\Auth
 *
 * @extends \Illuminate\Foundation\Http\FormRequest
 */
class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool True if the user is authorized, false otherwise.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the login request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * Get the custom validation messages for the login request.
     *
     * This method returns an associative array where the keys are the validation rule names
     * and the values are the corresponding custom error messages. These messages will be used
     * by the validator when validation fails for the login request.
     *
     * @return array<string, string> The array of custom validation messages.
     */
    public function messages(): array
    {
        return [
            'email.required' => "L'adresse e-mail est obligatoire.",
            'email.email' => "L'adresse e-mail n'est pas valide.",
            'password.required' => "Le mot de passe est obligatoire.",
        ];
    }
}

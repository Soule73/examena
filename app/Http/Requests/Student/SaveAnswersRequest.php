<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Handles validation for saving student answers.
 *
 * This request class is responsible for authorizing and validating
 * the input data when a student submits their answers.
 *
 * @package App\Http\Requests\Student
 */
class SaveAnswersRequest extends FormRequest
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
     * Get the validation rules that apply to the request.
     *
     * @return array The array of validation rules for saving student answers.
     */
    public function rules(): array
    {
        return [
            'answers' => ['required', 'array'],
        ];
    }
}

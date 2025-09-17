<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExamRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Pour l'instant, autoriser tous les utilisateurs authentifiés
        // TODO: Implémenter la vérification du rôle teacher et ownership
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'duration' => 'required|integer|min:1|max:480',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after:start_time',
            'is_active' => 'boolean',
            
            // Mêmes règles que StoreExamRequest pour les questions
            'questions' => 'required|array|min:1|max:50',
            'questions.*.content' => 'required|string|max:1000',
            'questions.*.type' => 'required|in:text,multiple_choice,true_false',
            'questions.*.points' => 'required|integer|min:1|max:100',
            
            'questions.*.choices' => 'required_if:questions.*.type,multiple_choice|array|min:2|max:6',
            'questions.*.choices.*.content' => 'required_if:questions.*.type,multiple_choice|string|max:255',
            'questions.*.correct_choice' => 'required_if:questions.*.type,multiple_choice|integer|min:0',
            
            'questions.*.correct_answer' => 'required_if:questions.*.type,true_false|in:true,false',
            'questions.*.suggested_answer' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Le titre de l\'examen est obligatoire.',
            'duration.required' => 'La durée de l\'examen est obligatoire.',
            'end_time.after' => 'La date de fin doit être postérieure à la date de début.',
            'questions.required' => 'Au moins une question est requise.',
            'questions.*.content.required' => 'Le contenu de la question est obligatoire.',
            'questions.*.type.required' => 'Le type de question est obligatoire.',
            'questions.*.points.required' => 'Le nombre de points est obligatoire.',
            'questions.*.choices.required_if' => 'Les choix sont obligatoires pour les questions à choix multiples.',
            'questions.*.correct_choice.required_if' => 'Vous devez sélectionner la bonne réponse.',
            'questions.*.correct_answer.required_if' => 'Vous devez indiquer si la réponse est vraie ou fausse.',
        ];
    }
}

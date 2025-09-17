<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;

class StoreExamRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Pour l'instant, autoriser tous les utilisateurs authentifiés
        // TODO: Implémenter la vérification du rôle teacher avec Spatie Permission
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
            'title' => 'required|string|min:3|max:255',
            'description' => 'nullable|string|max:1000',
            'duration' => 'required|integer|min:1|max:480', // 1 à 480 minutes (8h)
            'start_time' => 'nullable|date|after:now',
            'end_time' => 'nullable|date|after:start_time',
            'is_active' => 'boolean',
            
            // Validation des questions
            'questions' => 'required|array|min:1|max:50',
            'questions.*.content' => 'required|string|max:1000',
            'questions.*.type' => 'required|in:text,multiple_choice,true_false',
            'questions.*.points' => 'required|integer|min:1|max:100',
            
            // Validation pour questions à choix multiples
            'questions.*.choices' => 'required_if:questions.*.type,multiple_choice|array|min:2|max:6',
            'questions.*.choices.*.content' => 'required_if:questions.*.type,multiple_choice|string|max:255',
            'questions.*.correct_choice' => 'required_if:questions.*.type,multiple_choice|integer|min:0',
            
            // Validation pour questions vrai/faux
            'questions.*.correct_answer' => 'required_if:questions.*.type,true_false|in:true,false',
            
            // Validation pour questions texte (réponse suggérée optionnelle)
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
            'title.max' => 'Le titre ne peut pas dépasser 255 caractères.',
            'duration.required' => 'La durée de l\'examen est obligatoire.',
            'duration.min' => 'La durée minimum est de 1 minute.',
            'duration.max' => 'La durée maximum est de 480 minutes (8 heures).',
            'end_time.after' => 'La date de fin doit être postérieure à la date de début.',
            'questions.required' => 'Au moins une question est requise.',
            'questions.min' => 'L\'examen doit contenir au moins une question.',
            'questions.max' => 'L\'examen ne peut pas contenir plus de 50 questions.',
            'questions.*.content.required' => 'Le contenu de la question est obligatoire.',
            'questions.*.type.required' => 'Le type de question est obligatoire.',
            'questions.*.type.in' => 'Le type de question doit être : texte, choix multiple ou vrai/faux.',
            'questions.*.points.required' => 'Le nombre de points est obligatoire.',
            'questions.*.points.min' => 'Le minimum est 1 point par question.',
            'questions.*.choices.required_if' => 'Les choix sont obligatoires pour les questions à choix multiples.',
            'questions.*.choices.min' => 'Au moins 2 choix sont requis pour les questions à choix multiples.',
            'questions.*.correct_choice.required_if' => 'Vous devez sélectionner la bonne réponse.',
            'questions.*.correct_answer.required_if' => 'Vous devez indiquer si la réponse est vraie ou fausse.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'title' => 'titre',
            'description' => 'description',
            'duration' => 'durée',
            'start_time' => 'date de début',
            'end_time' => 'date de fin',
            'questions.*.content' => 'contenu de la question',
            'questions.*.type' => 'type de question',
            'questions.*.points' => 'points',
        ];
    }
}

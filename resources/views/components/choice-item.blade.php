@props([
    'questionIndex' => 0,
    'choiceIndex' => 0,
    'choice' => null,
])

<div class="flex items-center space-x-2">
    <input type="radio" name="questions[{{ $questionIndex }}][correct_choice]" value="{{ $choiceIndex }}"
        id="question_{{ $questionIndex }}_choice_{{ $choiceIndex }}" @if (old('questions.' . $questionIndex . '.correct_choice', $choice?->is_correct ? $choiceIndex : null) == $choiceIndex) checked @endif
        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">

    <x-input name="questions[{{ $questionIndex }}][choices][{{ $choiceIndex }}]" type="text"
        placeholder="Choix {{ chr(65 + $choiceIndex) }}" :value="old('questions.' . $questionIndex . '.choices.' . $choiceIndex, $choice->content ?? '')" required class="flex-1" />

    <label for="question_{{ $questionIndex }}_choice_{{ $choiceIndex }}" class="text-sm text-gray-500">
        Correcte
    </label>
</div>

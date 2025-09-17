@props([
    'questionIndex' => 0,
    'choiceIndex' => 0,
    'choice' => null,
    'showRemove' => true,
])

<div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg border border-gray-100">
    <input type="radio" x-bind:name="`questions[${questionIndex}][correct_choice]`" x-bind:value="choiceIndex"
        x-model="question.correct_choice" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">

    <input x-bind:name="`questions[${questionIndex}][choices][${choiceIndex}][content]`" type="text"
        x-model="choice.content" x-bind:placeholder="`Option ${String.fromCharCode(65 + choiceIndex)}`" required
        class="flex-1 px-3 py-2 border border-gray-200 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">

    <input type="hidden" x-bind:name="`questions[${questionIndex}][choices][${choiceIndex}][is_correct]`"
        x-bind:value="question.correct_choice == choiceIndex ? 1 : 0">

    @if ($showRemove)
        <button type="button" @click="removeChoice({{ $questionIndex }}, {{ $choiceIndex }})"
            x-show="question.choices.length > 2"
            class="inline-flex items-center p-1 text-red-600 hover:text-red-700 focus:outline-none transition-colors">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    @endif
</div>

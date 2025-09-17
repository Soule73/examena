@props([
    'index' => 0,
    'question' => null,
])

<div class="border border-gray-200 rounded-lg p-4" x-data="{ showChoices: true }">
    <div class="flex items-center justify-between mb-3">
        <h5 class="text-sm font-medium text-gray-900">Question {{ $index + 1 }}</h5>
        <button type="button" @click="showChoices = !showChoices" class="text-indigo-600 hover:text-indigo-500">
            <span x-show="!showChoices">Afficher</span>
            <span x-show="showChoices">Masquer</span>
        </button>
    </div>

    <div class="space-y-4">
        <div>
            <x-input name="questions[{{ $index }}][content]" type="text" placeholder="Texte de la question"
                :value="old('questions.' . $index . '.content', $question->content ?? '')" required class="w-full" />
        </div>

        <div>
            <x-input name="questions[{{ $index }}][points]" type="number" placeholder="Points" :value="old('questions.' . $index . '.points', $question->points ?? 1)"
                min="1" required class="w-24" />
        </div>

        <div x-show="showChoices" class="space-y-2">
            <h6 class="text-sm font-medium text-gray-700">Choix de réponses</h6>

            @for ($i = 0; $i < 4; $i++)
                <x-choice-item :question-index="$index" :choice-index="$i" :choice="isset($question->choices[$i]) ? $question->choices[$i] : null" />
            @endfor
        </div>
    </div>
</div>

@props(['questions', 'showActions' => false])

<div class="space-y-4">
    @forelse($questions as $index => $question)
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <div class="flex justify-between items-start mb-4">
                <h4 class="text-lg font-medium text-gray-900">Question {{ $index + 1 }}</h4>
                @if ($showActions)
                    <div class="flex space-x-2">
                        <x-button type="outline" size="sm" href="{{ route('teacher.questions.edit', $question) }}">
                            Modifier
                        </x-button>
                        <form action="{{ route('teacher.questions.destroy', $question) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <x-button type="outline" size="sm" onclick="return confirm('Êtes-vous sûr ?')">
                                Supprimer
                            </x-button>
                        </form>
                    </div>
                @endif
            </div>

            <div class="mb-4">
                <x-markdown-renderer :content="$question->content" />
                @if ($question->points)
                    <p class="text-sm text-gray-500 mt-1">{{ $question->points }} point(s)</p>
                @endif
            </div>

            <div class="space-y-2">
                <h5 class="text-sm font-medium text-gray-700">Choix de réponses :</h5>
                @foreach ($question->choices as $choiceIndex => $choice)
                    <div class="flex items-center space-x-2">
                        <span
                            class="inline-flex items-center justify-center h-6 w-6 rounded-full text-xs font-medium {{ $choice->is_correct ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ chr(65 + $choiceIndex) }}
                        </span>
                        <span class="text-gray-700 {{ $choice->is_correct ? 'font-medium' : '' }}">
                            {{ $choice->content }}
                        </span>
                        @if ($choice->is_correct)
                            <svg class="h-4 w-4 text-green-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <div class="text-center py-8 text-gray-500">
            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                </path>
            </svg>
            <p>Aucune question pour cet examen.</p>
        </div>
    @endforelse
</div>

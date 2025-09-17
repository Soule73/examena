@props(['exam'])

<div
    class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200 hover:shadow-md transition-shadow duration-200">
    <div class="p-6">
        <div class="flex items-center justify-between">
            <div class="flex-1 min-w-0">
                <h3 class="text-lg font-medium text-gray-900 truncate">
                    <a href="{{ route('teacher.exams.show', $exam) }}" class="hover:text-indigo-600">
                        {{ $exam->title }}
                    </a>
                </h3>

                @if ($exam->description)
                    <p class="mt-1 text-sm text-gray-600 line-clamp-2">{{ $exam->description }}</p>
                @endif

                <div class="mt-3 flex items-center space-x-4 text-sm text-gray-500">
                    <span class="flex items-center">
                        <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ $exam->duration }} min
                    </span>
                    <span class="flex items-center">
                        <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                        {{ $exam->questions_count ?? $exam->questions->count() }} questions
                    </span>
                    <span class="flex items-center">
                        <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                        {{ $exam->answers_count ?? 0 }} réponses
                    </span>
                </div>

                <div class="mt-2 text-xs text-gray-500">
                    Créé le {{ $exam->created_at->format('d/m/Y') }}
                </div>
            </div>

            <div class="flex flex-col items-end space-y-3 ml-4">
                @if ($exam->is_active)
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8">
                            <circle cx="4" cy="4" r="3" />
                        </svg>
                        Actif
                    </span>
                @else
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-gray-400" fill="currentColor" viewBox="0 0 8 8">
                            <circle cx="4" cy="4" r="3" />
                        </svg>
                        Inactif
                    </span>
                @endif

                <div class="flex items-center space-x-2">
                    <x-button type="outline" size="sm" href="{{ route('teacher.exams.show', $exam) }}">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                            </path>
                        </svg>
                    </x-button>

                    <x-button type="outline" size="sm" href="{{ route('teacher.exams.edit', $exam) }}">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                            </path>
                        </svg>
                    </x-button>

                    <form action="{{ route('teacher.exams.destroy', $exam) }}" method="POST" class="inline"
                        onsubmit="return confirm('Êtes-vous sûr ?')">
                        @csrf
                        @method('DELETE')
                        <x-button type="outline" size="sm" class="text-red-600 border-red-300 hover:bg-red-50">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                </path>
                            </svg>
                        </x-button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

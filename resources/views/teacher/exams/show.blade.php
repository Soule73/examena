<x-app-layout>
    <x-page-header title="{{ $exam->title }}" subtitle="Détails et gestion de l'examen" :back-route="route('teacher.exams.index')">
        <x-slot name="actions">
            <div class="flex items-center space-x-3">
                <x-button type="secondary" href="{{ route('teacher.exams.edit', $exam) }}">
                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                        </path>
                    </svg>
                    Modifier
                </x-button>

                <form action="{{ route('teacher.exams.duplicate', $exam) }}" method="POST" class="inline">
                    @csrf
                    <x-button type="outline">
                        <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z">
                            </path>
                        </svg>
                        Dupliquer
                    </x-button>
                </form>

                <form action="{{ route('teacher.exams.destroy', $exam) }}" method="POST" class="inline"
                    onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet examen ?')">
                    @csrf
                    @method('DELETE')
                    <x-button type="outline" class="text-red-600 border-red-300 hover:bg-red-50">
                        <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                            </path>
                        </svg>
                        Supprimer
                    </x-button>
                </form>
            </div>
        </x-slot>
    </x-page-header>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Statistiques -->
            <x-exam-stats :exam="$exam" />

            <!-- Informations de l'examen -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Informations de l'examen</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">Détails et paramètres de configuration</p>
                </div>
                <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        @if ($exam->description)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Description</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $exam->description }}</dd>
                            </div>
                        @endif

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Créé le</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $exam->created_at->format('d/m/Y à H:i') }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Dernière modification</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $exam->updated_at->format('d/m/Y à H:i') }}</dd>
                        </div>

                        @if ($exam->start_time)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Date de début</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $exam->start_time->format('d/m/Y à H:i') }}
                                </dd>
                            </div>
                        @endif

                        @if ($exam->end_time)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Date de fin</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $exam->end_time->format('d/m/Y à H:i') }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="bg-white shadow sm:rounded-lg mb-6">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Actions rapides</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">Gérez votre examen et ses paramètres</p>
                </div>
                <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <form action="{{ route('teacher.exams.toggle-active', $exam) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <x-button type="{{ $exam->is_active ? 'outline' : 'primary' }}" class="w-full">
                                @if ($exam->is_active)
                                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Désactiver l'examen
                                @else
                                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h8m-6 4h6m2-7a9 9 0 11-18 0 9 9 0 0118 0z">
                                        </path>
                                    </svg>
                                    Activer l'examen
                                @endif
                            </x-button>
                        </form>

                        <x-button type="outline" href="{{ route('teacher.exams.assign', $exam) }}" class="w-full">
                            <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                            Assigner aux étudiants
                        </x-button>

                        <x-button type="outline" href="{{ route('teacher.exams.assignments', $exam) }}" class="w-full">
                            <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                </path>
                            </svg>
                            Voir les assignations
                        </x-button>
                    </div>
                </div>
            </div>

            <!-- Questions -->
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Questions de l'examen</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">{{ $exam->questions->count() }} question(s) au
                        total</p>
                </div>
                <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                    <x-questions-list :questions="$exam->questions" />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

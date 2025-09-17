<x-app-layout>
    <x-page-header :title="'Étudiants assignés : ' . $exam->title" :subtitle="'Gérez les assignations pour cet examen'" />

    <div class="py-8">
        <!-- Informations sur l'examen -->
        <div class="bg-white rounded-xl border border-gray-100 overflow-hidden mb-8">
            <div class="px-8 py-6 border-b border-gray-50">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Informations sur l'examen</h3>
                    <a href="{{ route('teacher.exams.assign', $exam) }}"
                        class="inline-flex items-center justify-center font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition duration-150 ease-in-out bg-primary-600 text-white hover:bg-primary-700 focus:ring-primary-500 px-4 py-2 text-sm">
                        <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Assigner d'autres étudiants
                    </a>
                </div>
            </div>
            <div class="px-8 py-6">
                <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                    <div>
                        <label
                            class="block text-xs font-medium text-gray-700 uppercase tracking-wide mb-1">Titre</label>
                        <p class="text-sm text-gray-900">{{ $exam->title }}</p>
                    </div>
                    <div>
                        <label
                            class="block text-xs font-medium text-gray-700 uppercase tracking-wide mb-1">Durée</label>
                        <p class="text-sm text-gray-900">{{ $exam->duration }} minutes</p>
                    </div>
                    <div>
                        <label
                            class="block text-xs font-medium text-gray-700 uppercase tracking-wide mb-1">Questions</label>
                        <p class="text-sm text-gray-900">{{ $exam->questions->count() }} questions</p>
                    </div>
                    <div>
                        <label
                            class="block text-xs font-medium text-gray-700 uppercase tracking-wide mb-1">Assignés</label>
                        <p class="text-sm text-gray-900">{{ $assignedStudents->count() }} étudiants</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques des assignations -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
                <div class="px-6 py-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['assigned'] }}</p>
                            <p class="text-sm text-gray-500">Assignés</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
                <div class="px-6 py-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['started'] }}</p>
                            <p class="text-sm text-gray-500">Commencés</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
                <div class="px-6 py-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['submitted'] }}</p>
                            <p class="text-sm text-gray-500">Soumis</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
                <div class="px-6 py-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['graded'] }}</p>
                            <p class="text-sm text-gray-500">Corrigés</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Liste des étudiants assignés -->
        <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-50">
                <h3 class="text-lg font-semibold text-gray-900">Étudiants assignés ({{ $assignedStudents->count() }})
                </h3>
                <p class="mt-1 text-sm text-gray-500">Liste des étudiants qui ont accès à cet examen</p>
            </div>

            @if ($assignedStudents->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-8 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Étudiant
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Statut
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Assigné le
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Score
                                </th>
                                <th scope="col" class="relative px-6 py-3">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($assignedStudents as $assignment)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-8 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div
                                                    class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                    <span class="text-sm font-medium text-gray-700">
                                                        {{ strtoupper(substr($assignment->student->name, 0, 2)) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $assignment->student->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $assignment->student->email }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusConfig = [
                                                'assigned' => [
                                                    'bg' => 'bg-blue-100',
                                                    'text' => 'text-blue-800',
                                                    'label' => 'Assigné',
                                                ],
                                                'started' => [
                                                    'bg' => 'bg-yellow-100',
                                                    'text' => 'text-yellow-800',
                                                    'label' => 'En cours',
                                                ],
                                                'submitted' => [
                                                    'bg' => 'bg-green-100',
                                                    'text' => 'text-green-800',
                                                    'label' => 'Soumis',
                                                ],
                                                'graded' => [
                                                    'bg' => 'bg-purple-100',
                                                    'text' => 'text-purple-800',
                                                    'label' => 'Corrigé',
                                                ],
                                            ];
                                            $config = $statusConfig[$assignment->status] ?? $statusConfig['assigned'];
                                        @endphp
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $config['bg'] }} {{ $config['text'] }}">
                                            {{ $config['label'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $assignment->assigned_at->format('d/m/Y à H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if ($assignment->score !== null)
                                            <div class="flex items-center">
                                                <span class="font-medium">{{ $assignment->score }}/20</span>
                                                @if ($assignment->score >= 10)
                                                    <svg class="ml-1 w-4 h-4 text-green-500" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                            clip-rule="evenodd"></path>
                                                    </svg>
                                                @else
                                                    <svg class="ml-1 w-4 h-4 text-red-500" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                            clip-rule="evenodd"></path>
                                                    </svg>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-2">
                                            @if ($assignment->status === 'submitted' && $assignment->score === null)
                                                <a href="#"
                                                    class="text-blue-600 hover:text-blue-900 text-sm">Corriger</a>
                                            @endif

                                            <form
                                                action="{{ route('teacher.exams.assignment.remove', [$exam, $assignment->student]) }}"
                                                method="POST" class="inline-block"
                                                onsubmit="return confirm('Êtes-vous sûr de vouloir retirer cet étudiant de l\'examen ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 text-sm"
                                                    title="Retirer l'assignation">
                                                    Retirer
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z">
                        </path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun étudiant assigné</h3>
                    <p class="mt-1 text-sm text-gray-500">Cet examen n'a pas encore été assigné à des étudiants.</p>
                    <div class="mt-6">
                        <a href="{{ route('teacher.exams.assign', $exam) }}"
                            class="inline-flex items-center justify-center font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition duration-150 ease-in-out bg-primary-600 text-white hover:bg-primary-700 focus:ring-primary-500 px-4 py-2 text-sm">
                            <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Assigner des étudiants
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-between pt-8">
            <a href="{{ route('teacher.exams.show', $exam) }}"
                class="inline-flex items-center justify-center font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition duration-150 ease-in-out bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 focus:ring-primary-500 px-4 py-2 text-sm">
                <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Retour à l'examen
            </a>
        </div>
    </div>
</x-app-layout>

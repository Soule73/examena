<x-app-layout>
    <x-page-header :title="'Assigner l\'examen : ' . $exam->title" :subtitle="'Sélectionnez les étudiants qui doivent passer cet examen'" />

    <div class="py-8">
        <!-- Informations sur l'examen -->
        <div class="bg-white rounded-xl border border-gray-100 overflow-hidden mb-8">
            <div class="px-8 py-6 border-b border-gray-50">
                <h3 class="text-lg font-semibold text-gray-900">Informations sur l'examen</h3>
            </div>
            <div class="px-8 py-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 uppercase tracking-wide mb-1">Titre</label>
                        <p class="text-sm text-gray-900">{{ $exam->title }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 uppercase tracking-wide mb-1">Durée</label>
                        <p class="text-sm text-gray-900">{{ $exam->duration }} minutes</p>
                    </div>
                    <div>
                        <label
                            class="block text-xs font-medium text-gray-700 uppercase tracking-wide mb-1">Questions</label>
                        <p class="text-sm text-gray-900">{{ $exam->questions->count() }} questions</p>
                    </div>
                </div>
                @if ($exam->description)
                    <div class="mt-4">
                        <label
                            class="block text-xs font-medium text-gray-700 uppercase tracking-wide mb-1">Description</label>
                        <p class="text-sm text-gray-600">{{ $exam->description }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Formulaire d'assignation -->
        <form action="{{ route('teacher.exams.assign.store', $exam) }}" method="POST" x-data="studentAssignment()"
            class="space-y-8">
            @csrf

            <!-- Sélection des étudiants -->
            <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-50">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Sélectionner les étudiants</h3>
                            <p class="mt-1 text-sm text-gray-500">Choisissez les étudiants qui doivent passer cet examen
                            </p>
                        </div>
                        <div class="flex items-center space-x-3">
                            <span class="text-sm text-gray-600"
                                x-text="`${selectedCount} étudiant(s) sélectionné(s)`"></span>
                        </div>
                    </div>
                </div>

                <div class="px-8 py-6">
                    <!-- Actions en lot -->
                    <div class="flex items-center justify-between mb-6 p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center space-x-4">
                            <button type="button" @click="selectAll()"
                                class="inline-flex items-center px-3 py-2 text-xs font-medium text-blue-600 hover:text-blue-700 focus:outline-none transition-colors">
                                <svg class="-ml-0.5 mr-1 h-3 w-3" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                Sélectionner tout
                            </button>
                            <button type="button" @click="deselectAll()"
                                class="inline-flex items-center px-3 py-2 text-xs font-medium text-gray-600 hover:text-gray-700 focus:outline-none transition-colors">
                                <svg class="-ml-0.5 mr-1 h-3 w-3" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Désélectionner tout
                            </button>
                            <button type="button" @click="selectUnassigned()"
                                class="inline-flex items-center px-3 py-2 text-xs font-medium text-green-600 hover:text-green-700 focus:outline-none transition-colors">
                                <svg class="-ml-0.5 mr-1 h-3 w-3" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Sélectionner non-assignés
                            </button>
                        </div>
                        <div class="text-sm text-gray-600">
                            Total : {{ $students->count() }} étudiants
                        </div>
                    </div>

                    <!-- Liste des étudiants -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach ($students as $student)
                            @php
                                $isAssigned = in_array($student->id, $assignedStudentIds);
                            @endphp
                            <div
                                class="relative p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors
                                        {{ $isAssigned ? 'bg-blue-50 border-blue-200' : '' }}">
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" name="student_ids[]" value="{{ $student->id }}"
                                        x-model="selectedStudents" {{ $isAssigned ? 'checked' : '' }}
                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <div class="ml-3 flex-1">
                                        <div class="flex items-center justify-between">
                                            <p class="text-sm font-medium text-gray-900">{{ $student->name }}</p>
                                            @if ($isAssigned)
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    Déjà assigné
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-gray-500">{{ $student->email }}</p>
                                    </div>
                                </label>
                            </div>
                        @endforeach
                    </div>

                    @if ($students->isEmpty())
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z">
                                </path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun étudiant disponible</h3>
                            <p class="mt-1 text-sm text-gray-500">Il n'y a aucun étudiant enregistré dans le système.
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-100">
                <a href="{{ route('teacher.exams.show', $exam) }}"
                    class="inline-flex items-center justify-center font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition duration-150 ease-in-out bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 focus:ring-primary-500 px-4 py-2 text-sm">
                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Annuler
                </a>

                <button type="submit" x-bind:disabled="selectedCount === 0"
                    class="inline-flex items-center justify-center font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition duration-150 ease-in-out px-4 py-2 text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                    x-bind:class="selectedCount > 0 ? 'bg-primary-600 text-white hover:bg-primary-700 focus:ring-primary-500' :
                        'bg-gray-300 text-gray-500'">
                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    <span x-text="selectedCount > 0 ? `Assigner (${selectedCount})` : 'Assigner l\'examen'"></span>
                </button>
            </div>
        </form>
    </div>

    <script>
        function studentAssignment() {
            return {
                selectedStudents: @json($assignedStudentIds),

                get selectedCount() {
                    return this.selectedStudents.length;
                },

                selectAll() {
                    const allStudentIds = @json($students->pluck('id'));
                    this.selectedStudents = [...allStudentIds];
                },

                deselectAll() {
                    this.selectedStudents = [];
                },

                selectUnassigned() {
                    const allStudentIds = @json($students->pluck('id'));
                    const assignedIds = @json($assignedStudentIds);
                    const unassignedIds = allStudentIds.filter(id => !assignedIds.includes(id));
                    this.selectedStudents = [...assignedIds, ...unassignedIds];
                }
            }
        }
    </script>
</x-app-layout>

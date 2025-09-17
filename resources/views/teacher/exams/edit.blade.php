<x-app-layout>
    <x-page-header title="Modifier l'examen" :subtitle="'Modifiez les paramètres et questions de &quot;' . $exam->title . '&quot;'" :back-route="route('teacher.exams.show', $exam)" />

    <div class="py-8">
        <form action="{{ route('teacher.exams.update', $exam) }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')

            <!-- Informations générales -->
            <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">Informations générales</h3>
                    <p class="mt-1 text-sm text-gray-500">Configurez les paramètres de base de votre examen</p>
                </div>

                <div class="px-8 py-6 space-y-6">
                    <!-- Titre -->
                    <x-input name="title" label="Titre de l'examen"
                        placeholder="Ex: Examen de Mathématiques - Chapitre 5" :value="old('title', $exam->title)" required />

                    <!-- Description -->
                    <x-textarea name="description" label="Description (optionnelle)"
                        placeholder="Décrivez brièvement le contenu et les objectifs de cet examen" :value="old('description', $exam->description)"
                        rows="3" />

                    <!-- Paramètres temporels -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <x-input name="duration" type="number" label="Durée (minutes)" placeholder="60"
                            :value="old('duration', $exam->duration)" min="1" max="480" required />

                        <x-input name="start_time" type="datetime-local" label="Date et heure de début"
                            :value="old(
                                'start_time',
                                $exam->start_time ? $exam->start_time->format('Y-m-d\TH:i') : '',
                            )" />

                        <x-input name="end_time" type="datetime-local" label="Date et heure de fin" :value="old('end_time', $exam->end_time ? $exam->end_time->format('Y-m-d\TH:i') : '')" />
                    </div>

                    <!-- Activation -->
                    <x-checkbox name="is_active" label="Activer l'examen immédiatement"
                        description="Si activé, l'examen sera accessible aux étudiants selon les dates définies"
                        :checked="old('is_active', $exam->is_active)" />
                </div>
            </div>

            <!-- Questions -->
            <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">Questions</h3>
                    <p class="mt-1 text-sm text-gray-500">Modifiez les questions de votre examen</p>
                </div>

                <div class="px-8 py-6">
                    <x-advanced-questions-manager :exam="$exam" />
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-100">
                <x-button tag="a" :href="route('teacher.exams.show', $exam)" type="secondary">
                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Annuler
                </x-button>

                <x-button type="primary" form-type="submit">
                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                        </path>
                    </svg>
                    Mettre à jour l'examen
                </x-button>
            </div>

        </form>
    </div>
</x-app-layout>

<x-app-layout>
    <x-page-header title="Créer un nouvel examen" subtitle="Définissez les paramètres et questions de votre examen"
        :back-route="route('teacher.exams.index')" />

    <div class="py-8">
        <form action="{{ route('teacher.exams.store') }}" method="POST" class="space-y-8">
            @csrf

            <!-- Informations générales -->
            <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">Informations générales</h3>
                    <p class="mt-1 text-sm text-gray-500">Configurez les paramètres de base de votre examen</p>
                </div>

                <div class="px-8 py-6 space-y-6">
                    <!-- Titre -->
                    <x-input name="title" label="Titre de l'examen"
                        placeholder="Ex: Examen de Mathématiques - Chapitre 5" :value="old('title')" required />

                    <!-- Description -->
                    <x-textarea name="description" label="Description (optionnelle)"
                        placeholder="Décrivez brièvement le contenu et les objectifs de cet examen" :value="old('description')"
                        rows="3" />

                    <!-- Paramètres temporels -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <x-input name="duration" type="number" label="Durée (minutes)" placeholder="60"
                            :value="old('duration', 60)" min="1" max="480" required />

                        <x-input name="start_time" type="datetime-local" label="Date et heure de début"
                            :value="old('start_time')" />

                        <x-input name="end_time" type="datetime-local" label="Date et heure de fin" :value="old('end_time')" />
                    </div>

                    <!-- Activation -->
                    <x-checkbox name="is_active" label="Activer l'examen immédiatement"
                        description="Si activé, l'examen sera accessible aux étudiants selon les dates définies"
                        :checked="old('is_active')" />
                </div>
            </div>

            <!-- Questions -->
            <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">Questions</h3>
                    <p class="mt-1 text-sm text-gray-500">Ajoutez et configurez les questions de votre examen</p>
                </div>

                <div class="px-8 py-6">
                    <x-advanced-questions-manager />
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-100">
                <x-button tag="a" :href="route('teacher.exams.index')" type="secondary">
                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Annuler
                </x-button>

                <x-button type="primary" form-type="submit">
                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Créer l'examen
                </x-button>
            </div>

        </form>
    </div>
</x-app-layout>

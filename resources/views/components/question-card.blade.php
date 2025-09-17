@props([
    'index' => 0,
    'question' => null,
    'collapsed' => false,
])

<div x-data="{ collapsed: @js($collapsed) }" class="border border-gray-200 rounded-xl bg-white overflow-hidden">
    <!-- En-tête de la question -->
    <div class="px-6 py-4 bg-gray-50 cursor-pointer" @click="collapsed = !collapsed">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <svg class="h-4 w-4 text-gray-500 transition-transform" :class="{ 'rotate-90': !collapsed }"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <h4 class="text-sm font-semibold text-gray-900">Question {{ $index + 1 }}</h4>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                    :class="{
                        'bg-blue-100 text-blue-700': question.type === 'multiple_choice',
                        'bg-green-100 text-green-700': question.type === 'true_false',
                        'bg-purple-100 text-purple-700': question.type === 'text'
                    }"
                    x-text="getQuestionTypeLabel(question.type)">
                </span>
            </div>
            <div class="flex items-center space-x-3">
                <span class="text-xs text-gray-500" x-text="`${question.points} pts`"></span>
                {{ $actions ?? '' }}
            </div>
        </div>

        <!-- Aperçu du contenu quand collapsé -->
        <div x-show="collapsed" class="mt-2">
            <p class="text-sm text-gray-600 truncate" x-text="question.content || 'Aucun énoncé défini'"></p>
        </div>
    </div>

    <!-- Contenu de la question -->
    <div x-show="!collapsed" x-transition class="px-6 py-5 space-y-5 border-t border-gray-100">
        {{ $slot }}
    </div>
</div>

@props([
    'cancelRoute' => null,
    'submitText' => 'Enregistrer',
    'cancelText' => 'Annuler',
])

<div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-3">
    @if ($cancelRoute)
        <a href="{{ $cancelRoute }}"
            class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            {{ $cancelText }}
        </a>
    @endif

    <x-button type="submit">
        {{ $submitText }}
    </x-button>

    {{ $slot }}
</div>

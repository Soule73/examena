@props([
    'title' => 'Aucun élément trouvé',
    'message' => '',
    'icon' => 'document',
    'actionText' => null,
    'actionRoute' => null,
])

<div class="text-center py-12">
    @if ($icon === 'document')
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 48 48">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
            </path>
        </svg>
    @elseif($icon === 'users')
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 48 48">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M17 20h5v-2a3 3 0 10-6 0v2zm-2 4v8a2 2 0 002 2h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2z">
            </path>
        </svg>
    @elseif($icon === 'exam')
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 48 48">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 12h6m-6 4h6m-6 4h6m3 5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
    @else
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 48 48">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7h16zM4 20v16a2 2 0 002 2h12a2 2 0 002-2V20H4z"></path>
        </svg>
    @endif

    <h3 class="mt-4 text-lg font-medium text-gray-900">{{ $title }}</h3>

    @if ($message)
        <p class="mt-2 text-sm text-gray-500">{{ $message }}</p>
    @endif

    @if ($actionText && $actionRoute)
        <div class="mt-6">
            <x-button href="{{ $actionRoute }}">
                {{ $actionText }}
            </x-button>
        </div>
    @endif

    {{ $slot }}
</div>

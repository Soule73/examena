@props(['title', 'subtitle' => null, 'backUrl' => null, 'backText' => 'Retour'])

<div class="bg-white border border-gray-200 overflow-hidden sm:rounded-lg">
    <div class="p-6">
        <div class="flex items-center">
            @if ($backUrl)
                <x-button type="outline" size="sm" href="{{ $backUrl }}" class="mr-4">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                        </path>
                    </svg>
                    {{ $backText }}
                </x-button>
            @endif
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $title }}</h1>
                @if ($subtitle)
                    <p class="mt-2 text-sm text-gray-600">{{ $subtitle }}</p>
                @endif
            </div>
            @if (isset($actions))
                <div class="ml-auto">
                    {{ $actions }}
                </div>
            @endif
        </div>
    </div>
</div>

@props([
    'title' => null,
    'description' => null,
    'last' => false,
])

<div class="p-6 {{ $last ? '' : 'border-b border-gray-200' }}"
    @if ($title) <div class="mb-4">
            <h4 class="text-base font-medium text-gray-900">{{ $title }}</h4>
            @if ($description)
                <p class="mt-1 text-sm text-gray-600">{{ $description }}</p> @endif
    </div>
    @endif

    <div class="space-y-4">
        {{ $slot }}
    </div>
</div>

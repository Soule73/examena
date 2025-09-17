@props([
    'title' => '',
    'subtitle' => '',
    'padding' => 'px-8 py-6',
])

<div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
    @if ($title || $subtitle)
        <div class="{{ $padding }} border-b border-gray-50">
            @if ($title)
                <h3 class="text-lg font-semibold text-gray-900">{{ $title }}</h3>
            @endif
            @if ($subtitle)
                <p class="mt-1 text-sm text-gray-500">{{ $subtitle }}</p>
            @endif
        </div>
    @endif

    @if (isset($content))
        <div class="{{ $padding }}">
            {{ $content }}
        </div>
    @else
        {{ $slot }}
    @endif
</div>

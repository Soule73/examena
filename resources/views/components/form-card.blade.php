@props(['action', 'method' => 'POST', 'title' => null])

<form action="{{ $action }}" method="{{ $method === 'GET' ? 'GET' : 'POST' }}" {{ $attributes }}>
    @if ($method !== 'GET')
        @csrf
        @if ($method !== 'POST')
            @method($method)
        @endif
    @endif

    <div class="bg-white shadow-sm rounded-lg">
        @if ($title)
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">{{ $title }}</h3>
            </div>
        @endif

        {{ $slot }}
    </div>
</form>

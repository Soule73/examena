@props([
    'type' => 'info',
])

@php
    $typeClasses = match ($type) {
        'success' => 'bg-success-50 border-success-200 text-success-800',
        'error' => 'bg-danger-50 border-danger-200 text-danger-800',
        'warning' => 'bg-warning-50 border-warning-200 text-warning-800',
        'info' => 'bg-primary-50 border-primary-200 text-primary-800',
        default => 'bg-gray-50 border-gray-200 text-gray-800',
    };

    $iconClasses = match ($type) {
        'success' => 'text-success-500',
        'error' => 'text-danger-500',
        'warning' => 'text-warning-500',
        'info' => 'text-primary-500',
        default => 'text-gray-500',
    };
@endphp

<div {{ $attributes->merge(['class' => 'border rounded-md p-4 ' . $typeClasses]) }}>
    <div class="flex">
        <div class="flex-shrink-0">
            @if ($type === 'success')
                <svg class="h-5 w-5 {{ $iconClasses }}" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd" />
                </svg>
            @elseif($type === 'error')
                <svg class="h-5 w-5 {{ $iconClasses }}" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                        clip-rule="evenodd" />
                </svg>
            @elseif($type === 'warning')
                <svg class="h-5 w-5 {{ $iconClasses }}" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                        clip-rule="evenodd" />
                </svg>
            @else
                <svg class="h-5 w-5 {{ $iconClasses }}" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                        clip-rule="evenodd" />
                </svg>
            @endif
        </div>
        <div class="ml-3">
            {{ $slot }}
        </div>
    </div>
</div>

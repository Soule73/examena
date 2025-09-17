@props([
    'name' => '',
    'label' => '',
    'placeholder' => '',
    'required' => false,
    'value' => '',
    'error' => null,
    'rows' => 3,
])

<div class="mb-4">
    @if ($label)
        <label for="{{ $name }}" class="block text-xs font-medium text-gray-700 mb-2 uppercase tracking-wide">
            {{ $label }}
            @if ($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <textarea name="{{ $name }}" id="{{ $name }}" rows="{{ $rows }}" placeholder="{{ $placeholder }}"
        @if ($required) required @endif
        {{ $attributes->merge([
            'class' =>
                'block w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-none' .
                ($error ? ' border-red-500 focus:border-red-500 focus:ring-red-500' : ''),
        ]) }}>{{ old($name, $value) }}</textarea>

    @if ($error)
        <p class="mt-1 text-xs text-red-600">{{ $error }}</p>
    @endif

    @error($name)
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>

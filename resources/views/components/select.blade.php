@props([
    'name' => '',
    'label' => '',
    'required' => false,
    'value' => '',
    'error' => null,
    'options' => [],
    'placeholder' => 'Sélectionner...',
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

    <select name="{{ $name }}" id="{{ $name }}" @if ($required) required @endif
        {{ $attributes->merge([
            'class' =>
                'block w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors' .
                ($error ? ' border-red-500 focus:border-red-500 focus:ring-red-500' : ''),
        ]) }}>
        @if ($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif

        @if (!empty($options))
            @foreach ($options as $optionValue => $optionLabel)
                <option value="{{ $optionValue }}" @if (old($name, $value) == $optionValue) selected @endif>
                    {{ $optionLabel }}
                </option>
            @endforeach
        @else
            {{ $slot }}
        @endif
    </select>

    @if ($error)
        <p class="mt-1 text-xs text-red-600">{{ $error }}</p>
    @endif

    @error($name)
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>

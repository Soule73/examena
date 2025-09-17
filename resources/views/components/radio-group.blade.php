@props([
    'name' => '',
    'label' => '',
    'options' => [],
    'value' => '',
    'required' => false,
    'inline' => true,
])

<div class="space-y-3">
    @if ($label)
        <label class="block text-xs font-medium text-gray-700 mb-2 uppercase tracking-wide">
            {{ $label }}
            @if ($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <div class="{{ $inline ? 'flex space-x-4' : 'space-y-2' }}">
        @foreach ($options as $optionValue => $optionLabel)
            <label
                class="inline-flex items-center px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-100 transition-colors">
                <input type="radio" name="{{ $name }}" value="{{ $optionValue }}"
                    @if (old($name, $value) == $optionValue) checked @endif @if ($required) required @endif
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                <span class="ml-2 text-sm text-gray-900">{{ $optionLabel }}</span>
            </label>
        @endforeach
    </div>

    @error($name)
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>

@props([
    'name' => '',
    'label' => '',
    'description' => '',
    'required' => false,
    'value' => '1',
    'checked' => false,
])

<div class="flex items-start space-x-3 p-4 bg-gray-50 rounded-lg">
    <input type="checkbox" name="{{ $name }}" id="{{ $name }}" value="{{ $value }}"
        @if (old($name, $checked)) checked @endif @if ($required) required @endif
        class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
    <div>
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-900">
            {{ $label }}
            @if ($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
        @if ($description)
            <p class="text-xs text-gray-500 mt-1">
                {{ $description }}
            </p>
        @endif
    </div>

    @error($name)
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>

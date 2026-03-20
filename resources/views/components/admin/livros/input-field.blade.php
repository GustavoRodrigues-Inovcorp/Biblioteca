@props([
    'id',
    'name',
    'label',
    'type' => 'text',
    'value' => null,
])

<div>
    <label for="{{ $id }}" class="block text-sm font-medium text-gray-700">{{ $label }}</label>
    <x-input
        :id="$id"
        :name="$name"
        :type="$type"
        :value="$value"
        {{ $attributes->merge(['class' => 'mt-1 w-full']) }}
    ></x-input>

    @if (trim($slot) !== '')
        {{ $slot }}
    @endif

    @error($name)<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>

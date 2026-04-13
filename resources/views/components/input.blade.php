@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'text-black border-gray-300 focus:border-blue-800 focus:ring-blue-800 rounded-md shadow-sm']) !!}>

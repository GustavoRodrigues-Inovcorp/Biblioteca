@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 py-2 text-sm font-bold leading-5 text-blue-800 focus:outline-none transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 py-2 text-sm font-bold leading-5 text-slate-600 hover:font-semibold hover:text-blue-800 focus:font-semibold focus:text-blue-800 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>

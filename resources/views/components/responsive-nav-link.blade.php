@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 text-start text-base font-bold text-blue-800 bg-blue-50 focus:outline-none focus:text-blue-900 focus:bg-blue-100 transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 text-start text-base font-medium text-slate-700 hover:font-semibold hover:text-blue-800 hover:bg-slate-50 focus:outline-none focus:font-semibold focus:text-blue-800 focus:bg-slate-50 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>

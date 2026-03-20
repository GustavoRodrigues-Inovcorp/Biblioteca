@props([
    'href' => '#',
    'active' => false,
])

<a
    href="{{ $href }}"
    {{ $attributes->class([
        'flex items-center gap-3 rounded-lg px-3 py-2.5 transition',
        'bg-gray-800 text-white font-semibold' => $active,
        'text-gray-300 hover:bg-gray-800' => ! $active,
    ]) }}
>
    <span>{{ $slot }}</span>
</a>

@props([
    'placeholder' => 'Pesquisar...',
])

<div class="flex gap-3">
    <div class="relative w-full">
        <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 3.473 9.766l3.63 3.63a.75.75 0 1 0 1.06-1.06l-3.63-3.63A5.5 5.5 0 0 0 9 3.5ZM5 9a4 4 0 1 1 8 0 4 4 0 0 1-8 0Z" clip-rule="evenodd" />
        </svg>

        <x-input
            type="text"
            wire:model.live.debounce.400ms="search"
            placeholder="{{ $placeholder }}"
            class="w-full rounded-xl border py-2 pl-10 pr-3 text-sm"
        />
    </div>

</div>
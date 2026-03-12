<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
            <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-amber-50 text-amber-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 20h18" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 20V8l7-4 7 4v12" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 20v-5h6v5" />
                </svg>
            </span>
            {{ __('Editoras') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <livewire:editoras-table />
        </div>
    </div>
</x-app-layout>

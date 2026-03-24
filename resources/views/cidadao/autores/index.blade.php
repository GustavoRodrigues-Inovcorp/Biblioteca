<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-semibold text-slate-900">
            Autores
        </h1>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <livewire:autores-table />
        </div>
    </div>
</x-app-layout>

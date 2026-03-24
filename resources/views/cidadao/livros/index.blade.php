<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-semibold text-slate-900">
            Livros
        </h1>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif
            <livewire:livros-table :is-admin="false" />
        </div>
    </div>
</x-app-layout>

<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900">
                    Google Books
                </h1>
                <p class="text-sm text-slate-600">
                    Pesquise livros na API do Google Books e importe para a biblioteca.
                </p>
            </div>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="mb-4 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="mt-6">
        <livewire:googlebooks-table :books="$books ?? null" :existing-isbns="$existingIsbns ?? []" :q="$q ?? ''" :success="session('success')" />
    </div>
</x-admin-layout>

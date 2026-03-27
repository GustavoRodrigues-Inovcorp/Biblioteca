<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900">Livros</h1>
                <p class="text-sm text-slate-600">Gestão de livros.</p>
            </div>
            <div class="flex gap-2">
                <a
                    href="{{ route('admin.livros.export') }}"
                    class="inline-flex items-center rounded-md bg-white border border-emerald-200 px-4 py-2 text-xs font-semibold uppercase tracking-wider text-emerald-700 transition hover:bg-emerald-50"
                >
                    Exportar Excel
                </a>
                <a
                    href="{{ route('admin.googlebooks.index') }}"
                    class="inline-flex items-center rounded-md bg-white border border-blue-200 px-4 py-2 text-xs font-semibold uppercase tracking-wider text-blue-700 transition hover:bg-blue-50"
                >
                    Importar livro
                </a>
                <a
                    href="{{ route('admin.livros.create') }}"
                    class="inline-flex items-center rounded-md bg-green-600 px-4 py-2 text-xs font-semibold uppercase tracking-wider text-white transition hover:bg-emerald-700"
                >
                    Inserir livro
                </a>
            </div>
        </div>
    </x-slot>

    @if (session('status'))
        <div class="mb-4 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ session('status') }}
        </div>
    @endif

    <div class="mt-6">
        <livewire:livros-table :is-admin="true" />
    </div>
</x-admin-layout>

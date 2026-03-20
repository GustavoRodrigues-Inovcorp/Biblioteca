<x-admin-layout>
    @php($mostrarHistorico = request()->query('modo') === 'historico')

    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900">Requisições</h1>
                <p class="text-sm text-slate-600">Gestão de requisições de livros.</p>
            </div>
        </div>
    </x-slot>

    @if (session('status'))
        <div class="mb-4 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ session('status') }}
        </div>
    @endif

    <div class="mt-6 space-y-4">
        @if (! $mostrarHistorico)
            <livewire:livros-requisicao-table :is-admin="true" />
        @else
            <livewire:requisicao-table :is-admin="true" :user-id="null" />
        @endif
    </div>
</x-admin-layout>

<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900">Inserir livro</h1>
                <p class="text-sm text-slate-600">Cria um novo registo de livro.</p>
            </div>
        </div>
    </x-slot>

    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        @include('admin.livros.form', [
            'submitRoute' => route('admin.livros.store'),
            'method' => 'POST',
            'submitLabel' => 'Guardar',
            'editoras' => $editoras,
            'autores' => $autores,
            'livro' => null,
        ])
    </div>
</x-admin-layout>
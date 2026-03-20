<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Editar livro</h1>
                <p class="text-sm text-slate-600">Atualiza os dados do livro selecionado.</p>
            </div>
        </div>
    </x-slot>

    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        @include('admin.livros.form', [
            'submitRoute' => route('admin.livros.update', $livro),
            'method' => 'PUT',
            'submitLabel' => 'Atualizar',
            'editoras' => $editoras,
            'autores' => $autores,
            'livro' => $livro,
        ])
    </div>
</x-admin-layout>
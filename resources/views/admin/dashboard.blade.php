<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900">Dashboard Admin</h1>
                <p class="text-sm text-slate-600">Gestão da Biblioteca, Requisições e Utilizadores.</p>
            </div>
        </div>
    </x-slot>

    <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-admin.stat-card label="Livros" :value="$totalLivros" />
        <x-admin.stat-card label="Autores" :value="$totalAutores" />
        <x-admin.stat-card label="Editoras" :value="$totalEditoras" />
        <x-admin.stat-card label="Utilizadores Admin" :value="$totalAdmins" />
    </section>

    <section class="mt-6 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-slate-900">Atalhos rápidos</h2>

        <div class="mt-4 flex flex-wrap gap-3">
            <a href="{{ route('admin.livros.create') }}" class="inline-flex items-center rounded-md bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-600 focus:ring-offset-2">Inserir livro</a>
            <a href="{{ route('admin.admin-users.create') }}" class="inline-flex items-center rounded-md bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-600 focus:ring-offset-2">Criar administradores</a>
        </div>
    </section>
</x-admin-layout>

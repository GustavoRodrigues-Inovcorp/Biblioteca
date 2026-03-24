<x-admin-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-semibold text-slate-900">Pesquisar Livros na Google Books</h1>
    </x-slot>

    @if(session('success'))
        <div class="mb-4 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.googlebooks.search') }}" class="max-w-lg mt-6">
        @csrf
        <div class="mb-3">
            <label for="q" class="form-label">Título, autor ou ISBN</label>
            <input type="text" class="form-control" id="q" name="q" required>
        </div>
        <button type="submit" class="btn btn-primary">Pesquisar</button>
    </form>
</x-admin-layout>

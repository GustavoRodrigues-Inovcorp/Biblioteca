<x-admin-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-semibold text-slate-900">Resultados da Pesquisa Google Books</h1>
    </x-slot>

    <a href="{{ route('admin.googlebooks.index') }}" class="btn btn-secondary mb-3">Nova Pesquisa</a>
    @if(count($books) === 0)
        <div class="alert alert-warning">Nenhum livro encontrado.</div>
    @else
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Capa</th>
                    <th>Título</th>
                    <th>Autores</th>
                    <th>Editora</th>
                    <th>ISBN</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($books as $book)
                    @php
                        $info = $book['volumeInfo'] ?? [];
                        $isbn = collect($info['industryIdentifiers'] ?? [])->pluck('identifier')->implode(', ');
                        $autores = isset($info['authors']) ? implode(', ', $info['authors']) : '';
                        $capa = $info['imageLinks']['thumbnail'] ?? null;
                    @endphp
                    <tr>
                        <td>@if($capa)<img src="{{ $capa }}" alt="Capa" style="height:80px;">@endif</td>
                        <td>{{ $info['title'] ?? '' }}</td>
                        <td>{{ $autores }}</td>
                        <td>{{ $info['publisher'] ?? '' }}</td>
                        <td>{{ $isbn }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.googlebooks.import') }}">
                                @csrf
                                <input type="hidden" name="book" value='@json($book)'>
                                <button type="submit" class="btn btn-success btn-sm">Importar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</x-admin-layout>

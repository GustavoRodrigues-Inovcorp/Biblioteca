<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
            {{ __('Detalhes do Livro') }}
        </h2>
    </x-slot>
<div class="max-w-4xl mx-auto py-10">
    <div class="flex flex-col md:flex-row gap-8">
        <div class="flex-shrink-0">
            <div class="h-64 w-44 overflow-hidden rounded shadow bg-gray-100 flex items-center justify-center">
                @if ($livro->imagem_capa)
                    <img src="{{ str_starts_with($livro->imagem_capa, 'http') ? $livro->imagem_capa : asset('storage/' . $livro->imagem_capa) }}" alt="Capa de {{ $livro->nome }}" class="h-full w-full object-cover" />
                @else
                    <span class="text-gray-400 text-xs">Sem capa</span>
                @endif
            </div>
        </div>
        <div class="flex-1 space-y-2">
            <h1 class="text-2xl font-bold text-slate-800">{{ $livro->nome }}</h1>
            <div class="text-sm text-gray-500">ISBN: {{ $livro->isbn }}</div>
            <div class="text-sm text-gray-500">Autor(es):
                @forelse ($livro->autores as $autor)
                    <span class="inline-block bg-slate-100 rounded px-2 py-0.5 mr-1">{{ $autor->nome }}</span>
                @empty
                    <span class="text-gray-400">Sem autor</span>
                @endforelse
            </div>
            <div class="text-sm text-gray-500">Editora: {{ $livro->editora?->nome ?? '-' }}</div>
            <div class="text-sm text-gray-500">Preço: <span class="font-semibold text-slate-700">{{ number_format((float) $livro->preco, 2, ',', '.') }} EUR</span></div>
            <div class="mt-2 text-gray-700 text-sm">
                <span class="font-semibold">Bibliografia:</span>
                <div class="mt-1">{{ $livro->bibliografia ?? '-' }}</div>
            </div>
            <div class="mt-4">
                @if ($livro->isDisponivel())
                    @include('components.livros.requisitar-form', ['livro' => $livro, 'livrosRequisitados' => $livrosRequisitados ?? 0])
                @else
                    <span class="inline-block px-4 py-2 rounded text-sm text-gray-500">Indisponível</span>
                @endif
            </div>
        </div>
    </div>
    <div class="mt-10">
        <h2 class="pb-2 text-left text-sm font-semibold uppercase tracking-wider text-slate-500">Histórico de Requisições</h2>
        <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead>
                    <tr class="bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500">
                        <th class="px-4 py-3">Nº</th>
                        <th class="px-4 py-3">Utilizador</th>
                        <th class="px-4 py-3">Requisitado em</th>
                        <th class="px-4 py-3">Devolvido em</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse ($historico as $req)
                        <tr>
                            <td class="px-4 py-3">{{ $req->numero ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $req->user->name ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $req->requisitado_em ? \Carbon\Carbon::parse($req->requisitado_em)->format('d/m/Y') : '-' }}</td>
                            <td class="px-4 py-3">{{ $req->devolvido_em ? \Carbon\Carbon::parse($req->devolvido_em)->format('d/m/Y') : '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-xs text-slate-500">Sem histórico de requisições.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
</x-app-layout>

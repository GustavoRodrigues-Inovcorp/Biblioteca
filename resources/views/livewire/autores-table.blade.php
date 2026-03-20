<div class="flex gap-6 items-start">
    {{--
        Sidebar de filtros reutilizável.
        - sortOptions: opções de ordenação por nome (recente, A-Z, Z-A)
    --}}
    @include('livewire.components.filters-sidebar', [
        'sortOptions' => [
            [
                'preset' => 'recente',
                'label' => 'Recente',
                'isActive' => fn($dir) => $dir === 'normal',
            ],
            [
                'preset' => 'az',
                'label' => 'A-Z',
                'isActive' => fn($dir) => $dir === 'asc',
            ],
            [
                'preset' => 'za',
                'label' => 'Z-A',
                'isActive' => fn($dir) => $dir === 'desc',
            ],
        ],
        'currentSortDirection' => $sortDirection,
    ])

    <div class="flex-1 min-w-0">
        <div class="mb-4">
            @include('livewire.components.search-bar', [
                // A pesquisa filtra autores por Nome.
                'placeholder' => 'Pesquisar por Nome...',
            ])
        </div>

        {{-- Tabela principal de autores. --}}
        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500">
                            <th class="px-4 py-3 bg-slate-50 text-slate-500">Foto</th>
                            <th class="px-4 py-3 bg-slate-50 text-slate-500">
                                <button wire:click="sortBy" type="button" class="flex items-center gap-1 uppercase tracking-wider text-slate-500 text-xs font-Semibold focus:outline-none focus:ring-0 hover:text-blue-600">
                                    Nome
                                    @if ($sortDirection === 'asc')
                                        <span class="text-xs">↑</span>
                                    @elseif($sortDirection === 'desc')
                                        <span class="text-xs">↓</span>
                                    @endif
                                </button>
                            </th>
                            <th class="px-4 py-3 bg-slate-50 text-slate-500">Livros</th>
                            <th class="px-4 py-3 bg-slate-50 text-slate-500"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                    @php
                        $colspan = 3;
                        $autoresReturnQuery = array_filter([
                            'page' => $autores->currentPage() > 1 ? $autores->currentPage() : null,
                            'search' => $search !== '' ? $search : null,
                            'sortDirection' => $sortDirection !== 'normal' ? $sortDirection : null,
                        ], fn($value) => $value !== null && $value !== '');
                    @endphp
                    @forelse ($autores as $autor)
                        <tr>
                            <td class="px-4 py-3">
                                <div class="avatar">
                                    <div class="h-20 w-20 overflow-hidden rounded-lg shadow-sm ring-1 ring-black/5">
                                        @if ($autor->foto)
                                            <img src="{{ Storage::url($autor->foto) }}" alt="Foto de {{ $autor->nome }}" class="h-full w-full object-cover" />
                                        @else
                                            <div class="flex h-full w-full items-center justify-center bg-gray-200 text-xs text-gray-500">Sem foto</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="min-w-64 align-middle px-4 py-3">
                                <div class="font-semibold text-slate-900 text-sm">{{ $autor->nome }}</div>
                            </td>
                            <td class="min-w-96 px-4 py-3">
                                <div class="flex flex-wrap gap-2 items-center">
                                    @forelse ($autor->livros->take(5) as $livro)
                                        <div class="h-24 w-16 overflow-hidden rounded-sm bg-gray-100 shadow-sm ring-1 ring-black/5 flex items-center justify-center">
                                            @if ($livro->imagem_capa)
                                                <img src="{{ str_starts_with($livro->imagem_capa, 'http') ? $livro->imagem_capa : asset('storage/' . $livro->imagem_capa) }}" alt="Capa de {{ $livro->nome }}" class="h-full w-full object-cover" />
                                            @else
                                                <div class="flex h-full w-full items-center justify-center text-[10px] text-gray-400">N/A</div>
                                            @endif
                                        </div>
                                    @empty
                                        <span class="text-sm text-gray-400">Sem livros</span>
                                    @endforelse
                                    @if ($autor->livros_count > 5)
                                        <span class="badge badge-outline badge-sm bg-gray-100">+{{ $autor->livros_count - 5 }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="whitespace-nowrap text-right px-4 py-3">
                                <a href="{{ route('livros', ['search' => $autor->nome, 'return_to' => route('autores', $autoresReturnQuery) . '#autor-' . $autor->id]) }}"
                                    onclick="sessionStorage.setItem('biblioteca:return-scroll', JSON.stringify({ path: window.location.pathname + window.location.search, y: window.scrollY }))"
                                    class="inline-flex items-center rounded-md border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-700 transition hover:bg-slate-50 mr-1"                                    title="Ver livros de {{ $autor->nome }}"
                                    aria-label="Ver livros de {{ $autor->nome }}">
                                    Ver
                                </a>
                            </td>
                        </tr>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $colspan }}" class="px-4 py-8 text-center text-slate-500">Nenhum autor encontrado.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Paginação --}}
        <div class="mt-6">
            {{ $autores->links() }}
        </div>
    </div>
</div>

{{-- Script para restaurar a posição exata do scroll ao voltar desta página. --}}
<x-scroll-restore storage-key="biblioteca:return-scroll" />

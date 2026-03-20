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
            // A pesquisa filtra editoras pelo nome.
            'placeholder' => 'Pesquisar por Nome...',
        ])
    </div>

    {{-- Tabela principal de editoras. --}}
    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead>
                <tr class="bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500">
                    <th class="px-4 py-3 bg-slate-50 text-slate-500">Logótipo</th>
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

            <tbody class="text-black">
                @php
                    // A tabela tem 3 colunas fixas: logótipo, nome e livros.
                    $colspan = 3;

                    // Preserva o estado atual da listagem para o botão "Voltar"
                    // ao navegar para a página de livros de uma editora.
                    $editorasReturnQuery = array_filter([
                        'page' => $editoras->currentPage() > 1 ? $editoras->currentPage() : null,
                        'search' => $search !== '' ? $search : null,
                        'sortDirection' => $sortDirection !== 'normal' ? $sortDirection : null,
                    ], fn($value) => $value !== null && $value !== '');
                @endphp

                @forelse ($editoras as $editora)
                    <tr id="editora-{{ $editora->id }}" class="align-middle border-b">
                        <td class="px-4 py-3 align-middle">
                            <div class="avatar flex items-center">
                                <div class="h-20 w-20 overflow-hidden rounded-lg shadow-sm ring-1 ring-black/5">
                                    @if ($editora->logotipo)
                                        <img src="{{ str_starts_with($editora->logotipo, 'http') ? $editora->logotipo : asset('storage/' . $editora->logotipo) }}"
                                            alt="Logótipo de {{ $editora->nome }}"
                                            class="h-full w-full object-contain" />
                                    @else
                                        <div class="flex h-full w-full flex-col items-center justify-center bg-gray-400 text-center text-[10px] font-medium leading-tight text-white">
                                            <span>Sem logótipo</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="min-w-64 px-4 py-3 align-middle">
                            <div class="font-semibold flex items-center">{{ $editora->nome }}</div>
                        </td>
                        <td class="min-w-96 px-4 py-3 align-middle">
                            <div class="flex flex-wrap gap-2 items-center">
                                @forelse ($editora->livros->take(5) as $livro)
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
                                @if ($editora->livros_count > 5)
                                    <span class="badge badge-outline badge-sm bg-gray-100">+{{ $editora->livros_count - 5 }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="whitespace-nowrap text-right px-4 py-3 align-middle">
                            <a href="{{ route('livros', ['search' => $editora->nome, 'return_to' => route('editoras', $editorasReturnQuery) . '#editora-' . $editora->id]) }}"
                                onclick="sessionStorage.setItem('biblioteca:return-scroll', JSON.stringify({ path: window.location.pathname + window.location.search, y: window.scrollY }))"
                                class="inline-flex items-center rounded-md border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-700 transition hover:bg-slate-50"
                                title="Ver livros de {{ $editora->nome }}"
                                aria-label="Ver livros de {{ $editora->nome }}">
                                Ver
                            </a>
                        </td>
                    </tr>
                    </tr>
                @empty
                    <tr>
                        {{-- Mensagem apresentada quando a pesquisa não devolve editoras. --}}
                        <td colspan="{{ $colspan }}" class="text-center py-8 text-gray-500">
                            Nenhuma editora encontrada.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginação --}}
    <div class="mt-6">
        {{ $editoras->links() }}
    </div>
    </div>{{-- /main content --}}
</div>{{-- /flex wrapper --}}

{{-- Script para restaurar a posição exata do scroll ao voltar desta página. --}}
<x-scroll-restore storage-key="biblioteca:return-scroll" />

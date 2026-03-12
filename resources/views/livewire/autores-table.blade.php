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
        <div class="overflow-x-auto rounded-xl bg-white shadow">
            <table class="table">
                <thead>
                    <tr class="text-black">
                        <th>Foto</th>

                        {{--
                            Coluna Nome: clicável para alternar ordenação.
                            O componente AutoresTable::sortBy() cicla entre asc → desc → normal.
                        --}}
                        <th>
                            <button wire:click="sortBy" class="flex items-center gap-1 hover:text-blue-600">
                                Nome
                                @if ($sortDirection === 'asc')
                                    <span class="text-xs">↑</span>
                                @elseif($sortDirection === 'desc')
                                    <span class="text-xs">↓</span>
                                @endif
                            </button>
                        </th>

                        <th>Livros</th>
                    </tr>
                </thead>

                <tbody class="text-black">
                    @php
                        // A tabela tem 3 colunas fixas: foto, nome e livros.
                        $colspan = 3;

                        // Preserva o estado atual da listagem para o botão "Voltar"
                        // ao navegar para a página de livros de um autor.
                        $autoresReturnQuery = array_filter(
                            [
                                'page' => $autores->currentPage() > 1 ? $autores->currentPage() : null,
                                'search' => $search !== '' ? $search : null,
                                'sortDirection' => $sortDirection !== 'normal' ? $sortDirection : null,
                            ],
                            fn($value) => $value !== null && $value !== '',
                        );
                    @endphp

                    @forelse ($autores as $autor)
                        <tr id="autor-{{ $autor->id }}" class="align-top border-b">
                            {{-- Célula da Foto: mostra foto se existir (de URL externa ou do storage local). --}}
                            <td>
                                <div class="avatar">
                                    <div class="h-20 w-20 overflow-hidden rounded-md shadow-sm ring-1 ring-black/5">
                                        @if ($autor->foto)
                                            <img src="{{ str_starts_with($autor->foto, 'http') ? $autor->foto : asset('storage/' . $autor->foto) }}"
                                                alt="Foto de {{ $autor->nome }}" class="h-full w-full object-cover" />
                                        @else
                                            <div
                                                class="flex h-full w-full items-center justify-center bg-base-200 text-xs text-base-content/60">
                                                Sem foto
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td class="min-w-64">
                                <div class="font-semibold">{{ $autor->nome }}</div>
                            </td>

                            <td class="min-w-96">
                                {{-- Lista até 5 capas dos livros associados ao autor. --}}
                                <div class="flex items-center justify-between gap-3">
                                    <div class="flex flex-wrap gap-2">
                                        @forelse ($autor->livros->take(5) as $livro)
                                            <div
                                                class="group flex items-center gap-2 rounded-lg border border-gray-100 bg-gray-50">
                                                <div
                                                    class="h-24 w-16 overflow-hidden rounded-sm bg-gray-100 shadow-sm ring-1 ring-black/5">
                                                    @if ($livro->imagem_capa)
                                                        <img src="{{ str_starts_with($livro->imagem_capa, 'http') ? $livro->imagem_capa : asset('storage/' . $livro->imagem_capa) }}"
                                                            alt="Capa de {{ $livro->nome }}"
                                                            class="block h-full w-full object-cover object-center" />
                                                    @else
                                                        <div
                                                            class="flex h-full w-full items-center justify-center text-[10px]">
                                                            N/A
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @empty
                                            <span class="text-sm text-gray-400">Sem livros</span>
                                        @endforelse

                                        {{-- Indica quantos livros adicionais existem além dos 5 mostrados. --}}
                                        @if ($autor->livros_count > 5)
                                            <div class="flex items-center">
                                                <span class="badge badge-outline badge-sm bg-gray-100">
                                                    +{{ $autor->livros_count - 5 }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Atalho para abrir a listagem de livros já filtrada por este autor. --}}
                                    <a href="{{ route('livros', ['search' => $autor->nome, 'return_to' => route('autores', $autoresReturnQuery) . '#autor-' . $autor->id]) }}"
                                        onclick="sessionStorage.setItem('biblioteca:return-scroll', JSON.stringify({ path: window.location.pathname + window.location.search, y: window.scrollY }))"
                                        class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-md border border-gray-200 bg-white text-gray-600 transition hover:bg-gray-100 hover:text-gray-900"
                                        title="Ver livros de {{ $autor->nome }}"
                                        aria-label="Ver livros de {{ $autor->nome }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                            class="h-5 w-5">
                                            <path
                                                d="M12 5c-5.878 0-9.392 4.152-10.744 6.149a1.5 1.5 0 0 0 0 1.702C2.608 14.848 6.122 19 12 19s9.392-4.152 10.744-6.149a1.5 1.5 0 0 0 0-1.702C21.392 9.152 17.878 5 12 5Zm0 11a5 5 0 1 1 0-10 5 5 0 0 1 0 10Zm0-2.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z" />
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            {{-- Mensagem apresentada quando a pesquisa não devolve autores. --}}      
                            <td colspan="{{ $colspan }}" class="text-center py-8 text-gray-500">
                                Nenhum autor encontrado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginação --}}
        <div class="mt-6">
            {{ $autores->links() }}
        </div>
    </div>
</div>

{{-- Script para restaurar a posição exata do scroll ao voltar desta página. --}}
<x-scroll-restore storage-key="biblioteca:return-scroll" />

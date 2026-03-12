<div class="flex gap-6 items-start">
    {{--
        Sidebar de filtros reutilizável.
        - columnFilters: checkboxes para mostrar/ocultar colunas
        - sortOptions: opções de ordenação do título (recente, A-Z, Z-A)
        - showPriceRange: ativa os campos de filtro por preço mínimo e máximo
    --}}
    @include('livewire.components.filters-sidebar', [
        'columnFilters' => [
            ['model' => 'mostrarISBN',         'label' => 'ISBN'],
            ['model' => 'mostrarEditora',      'label' => 'Editora'],
            ['model' => 'mostrarBibliografia', 'label' => 'Bibliografia'],
        ],
        'sortOptions' => [
            [
                'preset' => 'normal',
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
        'showTitle' => true,
        'showPriceRange' => true,
        'minPriceModel' => 'precoMin',
        'maxPriceModel' => 'precoMax',
    ])

    <div class="flex-1 min-w-0">
    @php
        // Lê o parâmetro return_to da URL para mostrar o botão "Voltar" quando existe.
        // Exemplo de uso: /livros?return_to=/autores/1
        $returnTo = request()->query('return_to');
        $backHref = is_string($returnTo) && $returnTo !== '' ? $returnTo : null;
    @endphp

    {{-- Barra de ações: botão Voltar (opcional), barra de pesquisa e exportação para Excel --}}
    <div class="flex gap-3 mb-4">
        {{-- botão Voltar: Só aparece caso tenha vindo do botão de ver dos Autores ou das Editoras --}}
        @if ($backHref)
            <a href="{{ $backHref }}"
                class="inline-flex shrink-0 items-center rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-100">
                Voltar
            </a>
        @endif

        <div class="flex-1">
            @include('livewire.components.search-bar', [
                // A pesquisa filtra por ISBN, Título, Autor e Editora em simultâneo.
                'placeholder' => 'Pesquisar por ISBN, Título, Autor ou Editora...',
            ])
        </div>
        <x-button
            type="button"
            wire:click="exportarExcel"
            class="shrink-0 rounded-xl bg-green-600 hover:bg-green-700 text-white"
        >
            Exportar Excel
        </x-button>
    </div>

    {{-- Tabela principal de livros. As colunas opcionais são controladas pelos filtros da sidebar. --}}
    <div class="overflow-x-auto rounded-xl bg-white shadow">
        <table class="table">
            <thead>
                <tr class="text-black text-sm">
                    {{-- Coluna ISBN: visível apenas se o utilizador tiver o filtro ativo. --}}
                    @if($mostrarISBN)
                        <th>ISBN</th>
                    @endif

                    <th>Capa</th>

                    {{--
                        Coluna Título: clicável para alternar ordenação.
                        O componente LivrosTable::sortBy() cicla entre asc → desc → normal.
                    --}}
                    <th>
                        <button wire:click="sortBy" class="flex items-center gap-1 hover:text-blue-600">
                            Título
                            @if($sortDirection === 'asc')
                                <span class="text-xs">↑</span>
                            @elseif($sortDirection === 'desc')
                                <span class="text-xs">↓</span>
                            @endif
                        </button>
                    </th>

                    {{-- Colunas opcionais controladas pelos checkboxes da sidebar. --}}
                    @if($mostrarEditora)
                        <th>Editora</th>
                    @endif

                    @if($mostrarBibliografia)
                        <th>Bibliografia</th>
                    @endif

                    <th>Preço</th>
                </tr>
            </thead>

            <tbody class="text-black text-xs">
                @forelse ($livros as $livro)
                    <tr class="align-top border-b">
                        {{-- Célula ISBN opcional. --}}
                        @if($mostrarISBN)
                            <td class="whitespace-nowrap">{{ $livro->isbn }}</td>
                        @endif

                        {{-- Célula da capa: mostra imagem se existir (de URL externa ou do storage local). --}}
                        <td>
                            <div class="avatar">
                                <div class="h-24 w-16 overflow-hidden rounded-sm shadow-sm ring-1 ring-black/5">
                                    @if ($livro->imagem_capa)
                                        <img
                                            {{-- Suporta imagens externas (http) e imagens guardadas localmente no storage. --}}
                                            src="{{ str_starts_with($livro->imagem_capa, 'http') ? $livro->imagem_capa : asset('storage/' . $livro->imagem_capa) }}"
                                            alt="Capa de {{ $livro->nome }}"
                                            class="h-full w-full object-cover"
                                        />
                                    @else
                                        <div class="flex h-full w-full items-center justify-center bg-gray-200 text-xs text-gray-500">
                                            Sem capa
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <td class="min-w-64">
                            <div class="font-semibold text-sm">{{ $livro->nome }}</div>
                            {{-- Autores apresentados como badges por baixo do título. --}}
                            <div class="mt-2 flex flex-wrap gap-1.5">
                                @forelse ($livro->autores as $autor)
                                    <span class="badge badge-outline badge-sm bg-gray-100">{{ $autor->nome }}</span>
                                @empty
                                    <span class="text-sm text-gray-400">Sem autor</span>
                                @endforelse
                            </div>
                        </td>

                        @if($mostrarEditora)
                            <td class="whitespace-nowrap">{{ $livro->editora?->nome ?? '-' }}</td>
                        @endif

                        @if($mostrarBibliografia)
                            <td class="max-w-sm text-gray-700">
                                {{-- Str::limit trunca o texto a 90 caracteres para não quebrar o layout. --}}
                                {{ $livro->bibliografia ? \Illuminate\Support\Str::limit($livro->bibliografia, 90) : '-' }}
                            </td>
                        @endif

                        <td class="whitespace-nowrap font-medium">
                            {{ number_format((float) $livro->preco, 2, ',', '.') }} EUR
                        </td>
                    </tr>
                @empty
                    <tr>
                        {{--
                            colspan calculado dinamicamente conforme as colunas visíveis:
                            Capa + Título + Preço = 3 fixas, + as opcionais ativas (ISBN, Editora, Bibliografia).
                        --}}
                        <td colspan="@php echo (int) $mostrarISBN + 3 + (int) $mostrarEditora + (int) $mostrarBibliografia + 1; @endphp" class="text-center py-8 text-gray-500">
                            Nenhum livro encontrado.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginação --}}
    <div class="mt-6">
        {{ $livros->links() }}
    </div>
    </div>
</div>
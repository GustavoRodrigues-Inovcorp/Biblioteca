<div>
    <div class="flex gap-6 items-start">
    @php($forRequisicao = $forRequisicao ?? false)

    {{-- Só emitir o JS global para o número de livros requisitados se for utilizador autenticado e não admin --}}
    {{-- Só para utilizadores autenticados e não admins --}}

    @if (! $isAdmin)
        @include('livewire.components.filters-sidebar', [
            'columnFilters' => [
                ['model' => 'mostrarEditora', 'label' => 'Editora'],
                ['model' => 'mostrarBibliografia', 'label' => 'Bibliografia'],
            ],
            'sortOptions' => [
                [
                    'preset' => 'normal',
                    'label' => 'Recente',
                    'isActive' => fn ($dir) => $dir === 'normal',
                ],
                [
                    'preset' => 'az',
                    'label' => 'A-Z',
                    'isActive' => fn ($dir) => $dir === 'asc',
                ],
                [
                    'preset' => 'za',
                    'label' => 'Z-A',
                    'isActive' => fn ($dir) => $dir === 'desc',
                ],
            ],
            'currentSortDirection' => $sortDirection,
            'showTitle' => true,
            'showPriceRange' => true,
            'minPriceModel' => 'precoMin',
            'maxPriceModel' => 'precoMax',
        ])
    @endif

    <div class="flex-1 min-w-0">
        <div class="flex gap-3 mb-4">
            <div class="flex-1">
                @include('livewire.components.search-bar', [
                    'placeholder' => 'Pesquisar por ISBN, Titulo, Autor ou Editora...',
                ])
            </div>

            @if ($isAdmin && ! $forRequisicao && count($selectedLivros) > 0)
                <x-secondary-button
                    type="button"
                    wire:click="eliminarSelecionados"
                    wire:confirm="Tens a certeza que queres eliminar os livros selecionados?"
                    class="text-red-600 border-red-200 hover:bg-red-50 rounded-xl"
                >
                    Eliminar selecionados ({{ count($selectedLivros) }})
                </x-secondary-button>
            @endif

            @if (! $isAdmin && ! $forRequisicao)
                <x-button
                    type="button"
                    wire:click="exportarExcel"
                    class="shrink-0 rounded-xl bg-green-600 hover:bg-green-700 text-white"
                >
                    Exportar Excel
                </x-button>
            @endif
        </div>

        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500">
                        @if ($isAdmin && ! $forRequisicao)
                            <th scope="col" class="w-10 px-4 py-3 bg-slate-50 text-slate-500">
                                <input
                                    type="checkbox"
                                    wire:model.live="selectPage"
                                    class="rounded border-slate-300 text-slate-900 focus:ring-slate-500"
                                >
                            </th>
                        @endif
                        <th scope="col" class="px-4 py-3 bg-slate-50 text-slate-500">Capa</th>
                        <th scope="col" class="px-4 py-3 bg-slate-50 text-slate-500">
                            <button wire:click="sortBy" type="button" class="flex items-center gap-1 uppercase tracking-wider text-slate-500 text-xs font-Semibold focus:outline-none focus:ring-0 hover:text-blue-600">
                                Título
                                @if ($sortDirection === 'asc')
                                    <span class="text-xs">↑</span>
                                @elseif ($sortDirection === 'desc')
                                    <span class="text-xs">↓</span>
                                @endif
                            </button>
                        </th>
                        @if ($mostrarEditora)
                            <th scope="col" class="px-4 py-3 bg-slate-50 text-slate-500">Editora</th>
                        @endif
                        @if ($mostrarBibliografia)
                            <th scope="col" class="px-4 py-3 bg-slate-50 text-slate-500">Bibliografia</th>
                        @endif
                        <th scope="col" class="px-4 py-3 bg-slate-50 text-slate-500">Preço</th>
                        @if ($isAdmin || $forRequisicao)
                            <th scope="col" class="px-4 py-3 bg-slate-50 text-slate-500"></th>
                        @else
                            <th class="px-4 py-3 bg-slate-50 text-slate-500"></th>
                        @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse ($livros as $livro)
                        <tr
                            class="transition hover:bg-slate-50 cursor-pointer"
                            onclick="if(!['BUTTON','A','INPUT','LABEL','SELECT','TEXTAREA'].includes(event.target.tagName)){window.location='{{ $isAdmin ? route('admin.livros.show', $livro->id) : route('livros.show', $livro->id) }}'}"
                        >
                            @if ($isAdmin && ! $forRequisicao)
                                <td class="align-middle px-4 py-3">
                                    <input
                                        type="checkbox"
                                        wire:model.live="selectedLivros"
                                        value="{{ $livro->id }}"
                                        class="rounded border-slate-300 text-slate-900 focus:ring-slate-500"
                                    >
                                </td>
                            @endif
                            <td class="px-4 py-3">
                                <div class="avatar">
                                    <div class="h-24 w-16 overflow-hidden rounded-sm shadow-sm ring-1 ring-black/5">
                                        @if ($livro->imagem_capa)
                                            <img
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
                            <td class="min-w-64 align-middle px-4 py-3">
                                <div class="font-semibold text-slate-900 text-sm">{{ $livro->nome }}</div>
                                <div class="text-xs text-gray-500 mt-1">ISBN: {{ $livro->isbn }}</div>
                                <div class="mt-2 flex flex-wrap gap-1.5">
                                    @forelse ($livro->autores as $autor)
                                        <span class="badge badge-outline badge-sm bg-gray-100">{{ $autor->nome }}</span>
                                    @empty
                                        <span class="text-sm text-gray-400">Sem autor</span>
                                    @endforelse
                                </div>
                            </td>
                            @if ($mostrarEditora)
                                <td class="whitespace-nowrap px-4 py-3">{{ $livro->editora?->nome ?? '-' }}</td>
                            @endif
                            @if ($mostrarBibliografia)
                                <td class="max-w-sm text-gray-700 px-4 py-3">
                                    {{ $livro->bibliografia ? \Illuminate\Support\Str::limit($livro->bibliografia, 90) : '-' }}
                                </td>
                            @endif
                            <td class="whitespace-nowrap font-medium px-4 py-3">
                                {{ number_format((float) $livro->preco, 2, ',', '.') }} EUR
                            </td>
                            <td class="whitespace-nowrap text-right px-4 py-3">
                                @if ($isAdmin)
                                    <div class="flex items-center gap-2 justify-end">
                                        @include('components.livros.admin-actions', ['livro' => $livro])
                                        @if ($livro->isDisponivel())
                                            @include('components.livros.requisitar-form', ['livro' => $livro, 'livrosRequisitados' => $livrosRequisitados])
                                        @else
                                            <span class="text-gray-400 text-sm">Indisponível</span>
                                        @endif
                                    </div>
                                @elseif ($livro->isDisponivel())
                                    @include('components.livros.requisitar-form', ['livro' => $livro, 'livrosRequisitados' => $livrosRequisitados])
                                @else
                                    <span class="text-gray-400 text-xs">Indisponível</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ (int) $mostrarISBN + (int) $mostrarEditora + (int) $mostrarBibliografia + 3 + (($isAdmin && ! $forRequisicao) ? 1 : 0) + (($isAdmin || $forRequisicao) ? 1 : 0) }}" class="px-4 py-8 text-center text-slate-500">
                                Nenhum livro encontrado.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6">
            {{ $livros->links() }}
        </div>
    </div>
</div>
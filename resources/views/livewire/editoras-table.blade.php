<div>
    <div class="flex items-start gap-6">
        @include('livewire.components.filters-sidebar', [
            'sortOptions' => [
                [
                    'preset' => 'recente',
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
        ])

        <div class="min-w-0 flex-1">
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-5">
                @forelse ($editoras as $editora)
                    <a id="editora-{{ $editora->id }}" href="{{ route('editoras.show', $editora) }}"
                        onclick="sessionStorage.setItem('biblioteca:return-scroll', JSON.stringify({ path: window.location.pathname + window.location.search, y: window.scrollY }))"
                        class="group block overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                        <div class="aspect-[4/3] w-full overflow-hidden bg-white">
                            @if ($editora->logotipo)
                                <img src="{{ str_starts_with($editora->logotipo, 'http') ? $editora->logotipo : asset('storage/' . $editora->logotipo) }}" alt="Logótipo de {{ $editora->nome }}" class="h-full w-full object-contain transition duration-200 group-hover:scale-[1.02]" />
                            @else
                                <div class="flex aspect-[4/3] w-full items-center justify-center bg-gradient-to-br from-slate-200 to-slate-300 text-5xl font-semibold text-slate-500">
                                    {{ mb_strtoupper(mb_substr($editora->nome, 0, 1)) }}
                                </div>
                            @endif
                        </div>

                        <div class="p-4">
                            <h3 class="line-clamp-2 text-lg font-semibold text-slate-900">{{ $editora->nome }}</h3>
                            <p class="mt-1 text-sm text-slate-500">{{ $editora->livros_count }} {{ $editora->livros_count === 1 ? 'livro' : 'livros' }}</p>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full rounded-xl border border-slate-200 bg-white px-4 py-8 text-center text-sm text-slate-500 shadow-sm">
                        Nenhuma editora encontrada.
                    </div>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $editoras->links() }}
            </div>
        </div>
    </div>

    <x-scroll-restore storage-key="biblioteca:return-scroll" />
</div>


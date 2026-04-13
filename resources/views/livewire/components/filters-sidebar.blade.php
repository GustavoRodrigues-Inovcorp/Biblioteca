@props([
    'columnFilters' => [],
    'sortOptions' => [],
    'currentSortDirection' => 'normal',
    'showTitle' => false,
    'showPriceRange' => false,
    'minPriceModel' => 'precoMin',
    'maxPriceModel' => 'precoMax',
])

{{-- Sidebar de filtros reutilizável --}}
<aside class="sticky top-44 self-start w-52 shrink-0">
    <div class="overflow-y-auto rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
        @if ($showTitle)
            <h3 class="text-lg font-bold text-gray-800">Filtros</h3>
        @endif

        @if (count($sortOptions) > 0)
            @php
                // Se já existe conteúdo acima, aplica separador visual antes da ordenação.
                $hasSectionBeforeSort = $showTitle || count($columnFilters) > 0;
            @endphp

            <div class="{{ $hasSectionBeforeSort ? 'mt-5 border-t border-gray-100 pt-4' : '' }}">
                <p class="mb-2 text-sm font-bold text-gray-700">Ordenar por:</p>
                <div class="space-y-1 text-sm">
                    @foreach ($sortOptions as $option)
                        @php
                            // Determina se esta opção está ativa para destacar visualmente.
                            $isActive = $option['isActive']($currentSortDirection);
                        @endphp
                        <button wire:click="setSortPreset('{{ $option['preset'] }}')"
                            class="block w-full text-left py-0.5 transition-colors hover:text-blue-700 {{ $isActive ? 'text-blue-700' : 'text-gray-600' }}">
                            {{ $option['label'] }}
                        </button>
                    @endforeach
                </div>
            </div>
        @endif

        @if ($showPriceRange)
            @php
                // Controla o espaçamento/separador da secção de preço.
                $hasSectionBeforePrice = $showTitle || count($columnFilters) > 0 || count($sortOptions) > 0;
            @endphp

            <div class="{{ $hasSectionBeforePrice ? 'mt-5 border-t border-gray-100 pt-4' : '' }}">
                <p class="mb-2 text-sm font-bold text-gray-700">Preço</p>
                <div class="space-y-2">
                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-500">Min €</label>
                        <x-input type="number" min="0" step="0.01"
                            wire:model.live.debounce.400ms="{{ $minPriceModel }}" placeholder="0,00"
                            class="w-full text-sm"
                        />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-500">Max €</label>
                        <x-input type="number" min="0" step="0.01"
                            wire:model.live.debounce.400ms="{{ $maxPriceModel }}" placeholder="100,00"
                            class="w-full text-sm"
                        />
                    </div>
                </div>
            </div>
        @endif
    </div>
</aside>

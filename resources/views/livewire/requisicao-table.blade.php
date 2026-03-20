<div class="flex-1">
    <div class="flex gap-3 mb-4">
        <div class="flex-1">
            @if ($isAdmin)
                @include('livewire.components.search-bar', [
                    'placeholder' => 'Filtrar por Livro ou Utilizador...',
                ])
            @else
                @include('livewire.components.search-bar', [
                    'placeholder' => 'Pesquisar por Livro...',
                ])
            @endif
        </div>
    </div>
</div>

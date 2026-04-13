<aside x-data="{ openItems: false }" class="h-fit self-start border border-slate-200 bg-slate-50 p-7 text-slate-700 xl:sticky xl:top-20">
    <h2 class="text-2xl font-semibold text-slate-900">Resumo</h2>

    <button type="button" @click="openItems = !openItems" :class="openItems ? '' : 'border-b border-slate-300'" class="mt-8 flex w-full items-center justify-between border-t border-slate-300 py-5 text-left">
        <span class="text-lg font-semibold text-slate-900">{{ $totalItens }} Itens no Carrinho</span>
        <span class="text-2xl font-medium text-slate-900" x-text="openItems ? '−' : '+'"></span>
    </button>

    <div x-show="openItems" x-collapse class="border-b border-slate-300">
        <div class="max-h-[26rem] overflow-y-auto py-5 pr-2">
            <div class="space-y-5">
                @foreach ($itens as $item)
                    @php
                        $livro = $item['livro'];
                        $autor = $livro->autores->first();
                        $autorNome = $autor?->nome ?? 'Autor desconhecido';
                    @endphp

                    <article class="flex items-start gap-4 border-b border-slate-300 pb-5 last:border-b-0 last:pb-0">
                        <a href="{{ route('livros.show', $livro->id) }}" class="h-32 w-20 shrink-0 overflow-hidden bg-slate-100">
                            @if ($livro->imagem_capa)
                                <img src="{{ str_starts_with($livro->imagem_capa, 'http') ? $livro->imagem_capa : asset('storage/' . $livro->imagem_capa) }}"
                                    alt="Capa de {{ $livro->nome }}" class="h-full w-full object-cover" />
                            @else
                                <div class="flex h-full w-full items-center justify-center text-xs text-slate-400">Sem capa</div>
                            @endif
                        </a>

                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-3">
                                <a href="{{ route('livros.show', $livro->id) }}" class="line-clamp-2 text-lg font-semibold leading-snug text-slate-900 hover:text-slate-700">
                                    {{ $livro->nome }}
                                </a>
                                <span class="whitespace-nowrap text-lg font-bold text-red-600">{{ number_format((float) $item['subtotal'], 2, ',', '.') }} €</span>
                            </div>

                            <a href="{{ route('livros.index', ['search' => $autorNome]) }}" class="mt-1 block text-sm text-slate-700 underline underline-offset-2">
                                {{ $autorNome }}
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </div>

    <div class="mt-7 space-y-2 text-slate-500 text-md">
        <div class="flex items-center justify-between">
            <span>Subtotal s/IVA</span>
            <span>{{ number_format($subtotalSemIva, 2, ',', '.') }} €</span>
        </div>
        <div class="flex items-center justify-between">
            <span>Valor IVA</span>
            <span>{{ number_format($valorIva, 2, ',', '.') }} €</span>
        </div>
    </div>

    <div class="my-8 border-t border-slate-200"></div>

    <div class="mb-2">
        <div class="flex items-center justify-between text-lg font-bold text-slate-900">
            <span>TOTAL</span>
            <span class="text-red-600">{{ number_format($totalPreco, 2, ',', '.') }} €</span>
        </div>
    </div>
</aside>

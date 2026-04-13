@php
    $carrinho = collect(session('carrinho', []));
    $livros = \App\Models\Livro::whereIn('id', $carrinho->keys())->with('autores')->get()->keyBy('id');

    $itens = $carrinho->map(function ($quantidade, $livroId) use ($livros) {
        $livro = $livros->get((int) $livroId);

        if (! $livro) {
            return null;
        }

        $quantidade = (int) $quantidade;
        $subtotal = (float) $livro->preco * $quantidade;

        return [
            'livro' => $livro,
            'quantidade' => $quantidade,
            'subtotal' => $subtotal,
        ];
    })->filter()->values();

    $totalItens = (int) $itens->sum('quantidade');
    $totalPreco = (float) $itens->sum('subtotal');
@endphp

@if (session('cart_preview_open') && $itens->isNotEmpty())
    <div id="cart-preview-drawer" x-data="{ open: true }" x-show="open" x-cloak class="fixed inset-0 z-[60]">
        <div class="absolute inset-0 bg-slate-950/35" @click="open = false"></div>

        <aside class="absolute right-0 top-0 flex h-full w-full max-w-md flex-col bg-white shadow-2xl">
            <div class="flex items-center justify-between border-b border-slate-200 px-6 py-5">
                <h2 class="text-xl font-bold text-slate-900">Carrinho de Compras</h2>
                <button type="button" class="text-slate-400 transition hover:text-slate-700" @click="open = false" aria-label="Fechar carrinho">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-7 w-7" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6 6 18" />
                    </svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto px-6 py-5 bg-slate-50">
                <div class="space-y-6">
                    @foreach ($itens as $item)
                        @php
                            $livro = $item['livro'];
                            $autor = $livro->autores->first();
                            $autorNome = $autor?->nome ?? 'Autor desconhecido';
                            $subtotalAtual = (float) $item['subtotal'];
                            $subtotalAntigo = $subtotalAtual / 0.9;
                        @endphp

                        <div class="flex items-stretch gap-4">
                            <div class="h-30 w-20 shrink-0 overflow-hidden bg-slate-100 shadow-sm ring-1 ring-black/5">
                                <a href="{{ route('livros.show', $item['livro']->id) }}" class="h-44 w-28 shrink-0 overflow-hidden bg-slate-100">
                                    @if ($livro->imagem_capa)
                                        <img src="{{ str_starts_with($livro->imagem_capa, 'http') ? $livro->imagem_capa : asset('storage/' . $livro->imagem_capa) }}"
                                            alt="Capa de {{ $livro->nome }}" class="h-full w-full object-cover" />
                                    @endif
                                </a>
                            </div>

                            <div class="min-w-0 flex flex-1 flex-col">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="rounded-full bg-red-600 px-1.5 py-0.2 text-[8px] font-bold text-white">-10%</span>
                                    <form method="POST" action="{{ route('carrinho.remove', $livro->id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="keep_preview" value="1">
                                        <button type="submit" class="text-slate-400 transition hover:text-slate-700" title="Remover item">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4 mt-1">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0C9.91 2.48 9 3.464 9 4.645v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                                <div class="flex items-start justify-between gap-3">
                                    
                                    <div class="min-w-0">
                                        <a href="{{ route('livros.show', $item['livro']->id) }}" class="line-clamp-2 text-lg font-bold text-slate-900 hover:text-slate-700 md:text-md">
                                            {{ $item['livro']->nome }}
                                        </a>                                        
                                        <a href="{{ route('livros.index', ['search' => $autorNome]) }}" class="block text-xs text-blue-800 underline underline-offset-2">
                                            {{ $autorNome }}
                                        </a>
                                    </div>
                                </div>

                                @if ($item['quantidade'] > 1)
                                    <div class="mt-1 text-xs text-slate-500">Quantidade: {{ $item['quantidade'] }}</div>
                                @endif
                                
                                <div class="mt-auto flex items-baseline gap-2">
                                    <span class="text-xs text-slate-500 line-through">{{ number_format($subtotalAntigo, 2, ',', '.') }} €</span>
                                    <span class="font-extrabold text-red-600">{{ number_format($subtotalAtual, 2, ',', '.') }} €</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="border-t border-slate-200 px-6 py-5">
                <div class="flex items-center justify-between text-sm font-semibold text-slate-900">
                    <span>TOTAL</span>
                    <span class="text-red-600">{{ number_format($totalPreco, 2, ',', '.') }} €</span>
                </div>
                <div class="mt-1 text-xs text-slate-500">{{ $totalItens }} item(s) no carrinho</div>

                <div class="mt-5 space-y-3">
                    <a href="{{ route('checkout.morada-entrega') }}"
                        class="inline-flex w-full items-center justify-center rounded-md bg-blue-800 px-4 py-3 text-sm font-semibold text-white transition hover:bg-blue-900">
                        Comprar
                    </a>
                    <a href="{{ route('carrinho.index') }}"
                        class="inline-flex w-full items-center justify-center rounded-md border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50" @click="open = false">
                        Ver Carrinho de Compras
                    </a>
                </div>
            </div>
        </aside>
    </div>
@endif

<x-app-layout>
    <div class="py-10">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-6 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                    {{ session('success') }}

                    @if (session('multibanco_details'))
                        @php
                            $multibanco = session('multibanco_details');
                            $expiraEm = !empty($multibanco['expira_em']) ? \Carbon\Carbon::createFromTimestamp((int) $multibanco['expira_em'])->format('d/m/Y H:i') : null;
                        @endphp

                        <div class="mt-3 rounded border border-green-300 bg-white/80 p-3 text-xs sm:text-sm">
                            <p class="text-slate-900"><span class="font-semibold">Referência:</span> {{ $multibanco['referencia'] ?: '-' }}</p>
                            <p class="text-slate-900"><span class="font-semibold">Entidade:</span> {{ $multibanco['entidade'] ?: '-' }}</p>
                            <p class="text-slate-900"><span class="font-semibold">Valor:</span> {{ number_format((float) ($multibanco['valor'] ?? 0), 2, ',', '.') }} €</p>
                            @if ($expiraEm)
                                <p class="text-slate-900"><span class="font-semibold">Válido até:</span> {{ $expiraEm }}</p>
                            @endif
                            @if (!empty($multibanco['voucher_url']))
                                <p class="mt-1">
                                    <a href="{{ $multibanco['voucher_url'] }}" target="_blank" rel="noopener" class="text-blue-800 hover:text-blue-900 underline underline-offset-2">
                                        Ver comprovativo Multibanco
                                    </a>
                                </p>
                            @endif
                        </div>
                    @endif
                </div>
            @endif

            <div class="mb-12 flex items-center gap-2 text-sm">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-1 text-slate-800">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" class="h-4 w-4 shrink-0" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12 11.204 3.046a1.125 1.125 0 0 1 1.592 0L21.75 12M4.5 9.75V19.5A1.5 1.5 0 0 0 6 21h4.5v-5.25a1.5 1.5 0 0 1 1.5-1.5h0a1.5 1.5 0 0 1 1.5 1.5V21H18a1.5 1.5 0 0 0 1.5-1.5V9.75" />
                    </svg>
                    <span class="text-xs underline underline-offset-2 ml-2">Home</span>
                </a>
                <span class="text-xs text-slate-400">/</span>
                <span class="text-xs text-slate-500">Carrinho de Compras</span>
            </div>

            @if ($itens->isEmpty())
                <h1 class="text-2xl font-bold text-slate-800">Carrinho de Compras (0)</h1>
                <div class="mt-8 rounded-xl border border-slate-200 bg-white p-8 text-slate-600 shadow-sm">
                    <p class="text-sm">O carrinho esta vazio.</p>
                    <p class="mt-2 text-sm">Clique 
                        <a href="{{ route('livros.index') }}" class="text-blue-600 hover:text-blue-800 underline underline-offset-2">aqui</a> 
                        para explorar os nossos livros.
                    </p>
                </div>
            @else
                @php
                    $subtotalSemIva = $totalPreco / 1.06;
                    $valorIva = $totalPreco - $subtotalSemIva;
                @endphp

                <h1 class="mb-8 text-3xl font-bold tracking-tight text-slate-900 md:text-3xl">
                    Carrinho de Compras <span class="text-2xl md:text-2xl">({{ $totalItens }})</span>
                </h1>

                <div class="grid gap-8 xl:grid-cols-[minmax(0,1fr)_24rem]">
                    <section class="min-w-0">
                        @foreach ($itens as $item)
                            @php
                                $precoItem = (float) $item['subtotal'];
                                $precoAntigo = $precoItem / 0.8;
                                $autor = $item['livro']->autores->first();
                                $autorNome = $autor?->nome ?? 'Autor desconhecido';
                            @endphp
                            <article class="flex flex-wrap items-start gap-6 border-b border-slate-200 py-7 lg:flex-nowrap">
                                <a href="{{ route('livros.show', $item['livro']->id) }}" class="h-44 w-28 shrink-0 overflow-hidden bg-slate-100">
                                    @if ($item['livro']->imagem_capa)
                                        <img src="{{ str_starts_with($item['livro']->imagem_capa, 'http') ? $item['livro']->imagem_capa : asset('storage/' . $item['livro']->imagem_capa) }}"
                                            alt="Capa de {{ $item['livro']->nome }}" class="h-full w-full object-cover" />
                                    @else
                                        <div class="flex h-full w-full items-center justify-center text-xs text-slate-400">Sem capa</div>
                                    @endif
                                </a>

                                <div class="min-w-0 flex-1 pt-4">
                                    <div class="mb-3 flex w-full items-start justify-between gap-3 text-sm font-semibold text-white">
                                        <span class="rounded-full bg-red-600 px-2 py-0.5 text-[10px] font-bold text-white">-10%</span>

                                        <div class="ml-auto flex items-start gap-3">
                                            <div class="flex justify-end pb-1 text-right">
                                                <div class="flex items-baseline gap-2">
                                                    <span class="text-sm text-slate-500 line-through">
                                                        {{ number_format($precoAntigo, 2, ',', '.') }} €
                                                    </span>
                                                    <span class="ml-1 text-xl font-extrabold text-red-600">
                                                        {{ number_format($precoItem, 2, ',', '.') }} €
                                                    </span>
                                                </div>
                                            </div>

                                            <form method="POST" action="{{ route('carrinho.remove', $item['livro']->id) }}" class="self-start">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="pt-1 pl-10 inline-flex leading-none text-slate-500 transition hover:text-slate-700" title="Remover item">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0C9.91 2.48 9 3.464 9 4.645v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    <a href="{{ route('livros.show', $item['livro']->id) }}" class="line-clamp-2 text-lg font-bold text-slate-900 hover:text-slate-700 md:text-md">
                                        {{ $item['livro']->nome }}
                                    </a>
                                    @if ($autor)
                                        <a href="{{ route('livros.index', ['search' => $autorNome]) }}" class="mt-2 text-lg text-slate-600 md:text-sm underline underline-offset-2">{{ $autorNome }}</a>
                                    @else
                                        <span class="mt-2 text-lg text-slate-600 md:text-sm">{{ $autorNome }}</span>
                                    @endif
                                </div>

                            </article>
                        @endforeach
                    </section>

                    <aside class="h-fit self-start border border-slate-200 bg-slate-50 p-7 text-slate-700 xl:sticky xl:top-44">
                        <h2 class="text-2xl font-semibold text-slate-900">Resumo</h2>

                        <div class="mt-7 space-y-2 text-slate-500 text-md">
                            <div class="flex items-center justify-between">
                                <span>Subtotal</span>
                                <span>{{ number_format($totalPreco, 2, ',', '.') }} €</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>Valor IVA</span>
                                <span>{{ number_format($valorIva, 2, ',', '.') }} €</span>
                            </div>
                        </div>

                        <div class="my-8 border-t border-slate-200"></div>

                        <div class="mb-7">
                            <div class="flex items-center justify-between text-lg font-bold text-slate-900">
                                <span>TOTAL</span>
                                <span class="text-red-600">{{ number_format($totalPreco, 2, ',', '.') }} €</span>
                            </div>
                        </div>

                        <a href="{{ route('checkout.morada-entrega') }}"
                            class="inline-flex w-full items-center justify-center rounded-md px-4 py-2.5 text-sm border font-semibold uppercase tracking-wider transition bg-blue-800 text-white text-slate-800 hover:bg-blue-900">
                            Comprar
                        </a>
                    </aside>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

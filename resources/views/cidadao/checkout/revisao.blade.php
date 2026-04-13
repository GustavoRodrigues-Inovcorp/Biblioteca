<x-layouts.checkout :back-url="route('checkout.pagamento')" brand="INOVBOOKS">
    <div class="py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-8 flex items-center gap-2 text-sm">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-1 text-slate-800">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" class="h-4 w-4 shrink-0" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12 11.204 3.046a1.125 1.125 0 0 1 1.592 0L21.75 12M4.5 9.75V19.5A1.5 1.5 0 0 0 6 21h4.5v-5.25a1.5 1.5 0 0 1 1.5-1.5h0a1.5 1.5 0 0 1 1.5 1.5V21H18a1.5 1.5 0 0 0 1.5-1.5V9.75" />
                    </svg>
                    <span class="ml-2 text-xs underline underline-offset-2">Home</span>
                </a>
                <span class="text-xs text-slate-400">/</span>
                <a href="{{ route('carrinho.index') }}" class="text-xs text-slate-500 underline underline-offset-2">Carrinho de Compras</a>
                <span class="text-xs text-slate-400">/</span>
                <a href="{{ route('checkout.morada-entrega') }}" class="text-xs text-slate-500 underline underline-offset-2">Morada de Entrega</a>
                <span class="text-xs text-slate-400">/</span>
                <a href="{{ route('checkout.pagamento') }}" class="text-xs text-slate-500 underline underline-offset-2">Pagamento</a>
                <span class="text-xs text-slate-400">/</span>
                <span class="text-xs text-slate-500">Revisão</span>
            </div>

            {{-- Etapas --}}
            <div class="mb-6 flex overflow-x-auto justify-center">
                <div class="flex min-w-max items-start gap-3">
                    <a href="{{ route('checkout.morada-entrega') }}">
                        <div class="flex min-w-[92px] flex-col items-center gap-2">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-slate-200 text-sm font-semibold text-slate-600">1</span>
                            <span class="text-xs font-medium text-slate-500">Morada</span>
                        </div>
                    </a>
                    <span class="mt-4 h-px w-10 bg-slate-300"></span>
                    <a href="{{ route('checkout.pagamento') }}">
                        <div class="flex min-w-[92px] flex-col items-center gap-2">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-slate-200 text-sm font-semibold text-slate-600">2</span>
                            <span class="text-xs font-medium text-slate-500">Pagamento</span>
                        </div>
                    </a>
                    <span class="mt-4 h-px w-10 bg-slate-300"></span>
                    <div class="flex min-w-[92px] flex-col items-center gap-2">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-blue-800 text-sm font-semibold text-white">3</span>
                        <span class="text-xs font-medium text-blue-800">Revisão</span>
                    </div>
                </div>
            </div>

            <div class="grid gap-8 xl:grid-cols-[minmax(0,1fr)_28rem]">
                <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                    <div class="flex flex-wrap items-start gap-3">
                        <div>
                            <h1 class="text-2xl font-bold text-slate-900">Revisão</h1>
                            <p class="mt-2 text-sm text-slate-600">Confirma e finaliza a tua encomenda.</p>
                        </div>
                    </div>

                    @if (session('error'))
                        <div class="mt-6 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="mt-8 space-y-8">
                        <div class="rounded-lg border border-slate-200 p-5">
                            <div class="mb-4 flex items-center justify-between gap-2">
                                <h2 class="mb-2 text-base font-semibold text-slate-900">Livros na encomenda</h2>
                                <span class="text-sm text-slate-600">{{ $totalItens }} itens</span>
                            </div>

                            <div class="space-y-4">
                                @foreach ($itens as $item)
                                    @php
                                        $livro = $item['livro'];
                                        $autor = $livro->autores->first();
                                    @endphp
                                    <article class="flex items-start justify-between gap-4 border-b border-slate-200 pb-4 last:border-b-0 last:pb-0">
                                        <div class="flex min-w-0 items-start gap-3">
                                            <a href="{{ route('livros.show', $livro->id) }}" class="h-16 w-11 shrink-0 overflow-hidden rounded-sm bg-slate-100">
                                                @if ($livro->imagem_capa)
                                                    <img src="{{ str_starts_with($livro->imagem_capa, 'http') ? $livro->imagem_capa : asset('storage/' . $livro->imagem_capa) }}" alt="Capa de {{ $livro->nome }}" class="h-full w-full object-cover" />
                                                @endif
                                            </a>
                                            <div class="min-w-0">
                                                <p class="line-clamp-2 text-sm font-semibold text-slate-900">{{ $livro->nome }}</p>
                                                <p class="mt-0.5 text-xs text-slate-600">{{ $autor?->nome ?? 'Autor desconhecido' }}</p>
                                            </div>
                                        </div>
                                        <p class="shrink-0 text-sm font-semibold text-red-600">{{ number_format((float) $item['subtotal'], 2, ',', '.') }} €</p>
                                    </article>
                                @endforeach
                            </div>
                        </div>

                        <div class="grid gap-5 sm:grid-cols-2">
                            <div class="rounded-lg border border-slate-200 p-5">
                                <div class="mb-3 flex items-center justify-between gap-2">
                                    <h2 class="text-base font-semibold text-slate-900">Morada de entrega</h2>
                                    <a href="{{ route('checkout.morada-entrega') }}" class="text-xs font-semibold text-blue-800 underline underline-offset-2">Editar</a>
                                </div>
                                <div class="space-y-1 text-sm text-slate-700">
                                    <p class="font-semibold text-slate-900">{{ $moradaEntrega['nome'] }}</p>
                                    <p>{{ $moradaEntrega['morada'] }}</p>
                                    <p>{{ $moradaEntrega['codigo_postal'] }} {{ $moradaEntrega['localidade'] }}</p>
                                </div>
                            </div>

                            <div class="rounded-lg border border-slate-200 p-5">
                                <div class="mb-3 flex items-center justify-between gap-2">
                                    <h2 class="text-base font-semibold text-slate-900">Método de pagamento</h2>
                                    <a href="{{ route('checkout.pagamento') }}" class="text-xs font-semibold text-blue-800 underline underline-offset-2">Editar</a>
                                </div>

                                <p class="text-sm font-semibold text-slate-900">{{ $selectedPaymentMethod['label'] }}</p>
                                <p class="mt-1 text-sm text-slate-600">{{ $selectedPaymentMethod['description'] }}</p>

                                @if (($selectedPaymentMethod['code'] ?? '') === 'mb_way' && !empty($mbwayPhone))
                                    <p class="mt-2 text-sm text-slate-700">Telemóvel MB WAY: {{ $mbwayPhone }}</p>
                                @endif

                                <div class="mt-4">
                                    @if ($selectedPaymentMethod['code'] === 'card')
                                        <span class="inline-flex h-10 items-center gap-2">
                                            <img src="{{ asset('images/payments/visa.png') }}" alt="Visa" class="h-4 w-auto object-contain">
                                            <img src="{{ asset('images/payments/mastercard.png') }}" alt="Mastercard" class="h-4 w-auto object-contain">
                                        </span>
                                    @elseif ($selectedPaymentMethod['code'] === 'mb_way')
                                        <img src="{{ asset('images/payments/mbWay.png') }}" alt="MB WAY" class="h-7 w-auto object-contain">
                                    @elseif ($selectedPaymentMethod['code'] === 'multibanco')
                                        <img src="{{ asset('images/payments/multibanco.png') }}" alt="Multibanco" class="h-6 w-auto object-contain">
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('checkout.pagamento.stripe') }}" class="mt-8">
                        @csrf
                        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                            <x-button>
                                Finalizar encomenda
                            </x-button>
                        </div>
                    </form>
                </section>

                <aside class="h-fit self-start border border-slate-200 bg-slate-50 p-7 text-slate-700 xl:sticky xl:top-44">
                    <div class="mt-2 space-y-2 text-slate-500 text-md">
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

                    <div class="mb-2 space-y-2">
                        <div class="flex items-center justify-between text-lg font-bold text-slate-900">
                            <span>TOTAL</span>
                            <span class="text-red-600">{{ number_format($totalPreco, 2, ',', '.') }} €</span>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</x-layouts.checkout>

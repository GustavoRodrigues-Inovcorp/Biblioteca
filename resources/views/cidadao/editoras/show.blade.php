<x-app-layout>
    <div class="py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-6 flex items-center gap-2 text-sm">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-1 text-slate-800">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" class="h-4 w-4 shrink-0" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12 11.204 3.046a1.125 1.125 0 0 1 1.592 0L21.75 12M4.5 9.75V19.5A1.5 1.5 0 0 0 6 21h4.5v-5.25a1.5 1.5 0 0 1 1.5-1.5h0a1.5 1.5 0 0 1 1.5 1.5V21H18a1.5 1.5 0 0 0 1.5-1.5V9.75" />
                    </svg>
                    <span class="ml-2 text-xs underline underline-offset-2">Home</span>
                </a>
                <span class="text-xs text-slate-400">/</span>
                <a href="{{ route('editoras.index') }}" class="text-xs text-slate-500 underline underline-offset-2">Editoras</a>
                <span class="text-xs text-slate-400">/</span>
                <span class="text-xs text-slate-500">{{ $editora->nome }}</span>
            </div>

            <div class="mb-10 flex flex-col gap-5 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:flex-row sm:items-center sm:p-6">
                <div class="h-28 w-28 shrink-0 overflow-hidden rounded-xl border border-slate-200 bg-slate-100">
                    @if ($editora->logotipo)
                        <img src="{{ str_starts_with($editora->logotipo, 'http') ? $editora->logotipo : asset('storage/' . $editora->logotipo) }}" alt="Logótipo de {{ $editora->nome }}" class="h-full w-full object-contain" />
                    @else
                        <div class="flex h-full w-full items-center justify-center text-4xl font-semibold text-slate-500">
                            {{ mb_strtoupper(mb_substr($editora->nome, 0, 1)) }}
                        </div>
                    @endif
                </div>

                <div>
                    <h1 class="text-3xl font-bold tracking-tight text-slate-900">{{ $editora->nome }}</h1>
                    <p class="mt-2 text-sm text-slate-600">{{ $editora->livros->count() }} {{ $editora->livros->count() === 1 ? 'livro associado' : 'livros associados' }}</p>
                </div>
            </div>

            <div class="border-t border-slate-200">
                <h2 class="pt-10 pb-8 text-2xl font-bold text-slate-900">Catálogo da Editora</h2>
            </div>

            @if ($editora->livros->isNotEmpty())
                <div class="grid grid-cols-2 gap-4 pb-2 sm:grid-cols-4 lg:grid-cols-6">
                    @foreach ($editora->livros as $livro)
                        @php
                            $precoAtual = (float) $livro->preco;
                            $precoAntigo = $precoAtual > 0 ? $precoAtual / 0.9 : 0;
                            $autorPrincipal = $livro->autores->first()->nome ?? 'Autor desconhecido';
                            $livroNoCarrinho = array_key_exists((string) $livro->id, session('carrinho', []));
                        @endphp

                        <article class="group min-w-0 overflow-hidden">
                            <div class="block transition hover:opacity-90">
                                <div class="relative w-full overflow-hidden bg-white">
                                    <a href="{{ route('livros.show', $livro->id) }}" class="block transition">
                                        <span class="absolute left-2 top-2 z-10 rounded-full bg-red-600 px-1.5 py-0.5 text-[11px] font-bold text-white">-10%</span>
                                        @if ($livro->imagem_capa)
                                            <img
                                                src="{{ str_starts_with($livro->imagem_capa, 'http') ? $livro->imagem_capa : asset('storage/' . $livro->imagem_capa) }}"
                                                alt="Capa de {{ $livro->nome }}"
                                                class="h-66 w-full object-cover"
                                            />
                                        @else
                                            <div class="flex h-full w-full items-center justify-center text-sm text-slate-500">Sem capa</div>
                                        @endif
                                    </a>
                                    @auth
                                        @if (!auth()->user()->isAdmin())
                                            <div class="pointer-events-none absolute inset-0 flex items-end bg-gradient-to-t from-slate-950/70 via-slate-950/20 to-transparent opacity-0 transition duration-200 group-hover:opacity-100 group-focus-within:opacity-100">
                                                <div class="flex w-full p-3 pointer-events-auto">
                                                    <form method="POST" action="{{ route('carrinho.add', $livro->id) }}" class="w-full">
                                                        @csrf
                                                        <button type="submit" {{ $livroNoCarrinho ? 'disabled' : '' }}
                                                            class="inline-flex w-full items-center justify-center rounded-md px-3 py-2 text-xs font-semibold shadow-sm transition {{ $livroNoCarrinho ? 'cursor-not-allowed bg-slate-200 text-slate-500' : 'bg-blue-800 text-white hover:bg-blue-900' }}">
                                                            {{ $livroNoCarrinho ? 'No carrinho' : 'Adicionar ao carrinho' }}
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        @endif
                                    @endauth
                                </div>
                                <div class="pt-1.5">
                                    <h3 class="line-clamp-2 text-[15px] font-semibold leading-snug text-slate-800">{{ $livro->nome }}</h3>
                                    <p class="mt-0.5 text-[12px] text-slate-600">de {{ $autorPrincipal }}</p>
                                    <div class="mt-1 flex items-baseline gap-1">
                                        <span class="text-md font-bold text-red-600">{{ number_format($precoAtual, 2, ',', '.') }} €</span>
                                        <span class="text-[12px] text-slate-500 line-through">{{ number_format($precoAntigo, 2, ',', '.') }} €</span>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="rounded-xl border border-slate-200 bg-white px-4 py-8 text-center text-sm text-slate-500 shadow-sm">
                    Esta editora ainda não tem livros associados.
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

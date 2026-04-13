<x-app-layout>
    <div class="max-w-6xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
        {{--
            Detalhes do livro, incluindo capa, título, autores, editora, preço e bibliografia.
            Se o livro estiver disponível, mostra botão para requisitar
            Se o utilizador tiver requisitado e devolvido o livro, mostra formulário para submeter um review.
            Mostra também uma lista de reviews ativos para o livro.
            Sugere livros relacionados com base em palavras comuns na bibliografia.
        --}}

        @php
            $alertaAtivo = auth()->check() && auth()->user()->alertasLivro->contains('livro_id', $livro->id);
            $livroNoCarrinho = array_key_exists((string) $livro->id, session('carrinho', []));
            $precoAtualDetalhe = (float) $livro->preco;
            $precoAntigoDetalhe = $precoAtualDetalhe > 0 ? $precoAtualDetalhe / 0.9 : 0;
        @endphp

        {{-- Cabeçalho do Livro --}}
        <div class="mb-6 flex items-center gap-2 text-sm">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-1 text-slate-800">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" class="h-4 w-4 shrink-0" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12 11.204 3.046a1.125 1.125 0 0 1 1.592 0L21.75 12M4.5 9.75V19.5A1.5 1.5 0 0 0 6 21h4.5v-5.25a1.5 1.5 0 0 1 1.5-1.5h0a1.5 1.5 0 0 1 1.5 1.5V21H18a1.5 1.5 0 0 0 1.5-1.5V9.75" />
                </svg>  
                <span class="text-xs underline underline-offset-2 ml-2">Home</span>
            </a>
            <span class="text-xs text-slate-400">/</span>
            <a href="{{ route('livros.index') }}" class="text-xs text-slate-500 underline underline-offset-2">Livros</a>
            <span class="text-xs text-slate-400">/</span>
            <span class="text-xs text-slate-500">{{ $livro->nome }}</span>
        </div>

        <div class="grid gap-8 lg:grid-cols-[18rem_minmax(0,1fr)] items-start">
            <aside class="lg:sticky lg:top-6 self-start">
                <div class="h-[29rem] w-full overflow-hidden shadow-sm">
                    @if ($livro->imagem_capa)
                          <span class="absolute left-3 top-3 z-10 rounded-full bg-red-600 px-2 py-1 text-[12px] font-bold text-white">-10%</span>
                        <img src="{{ str_starts_with($livro->imagem_capa, 'http') ? $livro->imagem_capa : asset('storage/' . $livro->imagem_capa) }}"
                            alt="Capa de {{ $livro->nome }}" class="h-full w-full object-cover" />
                    @else
                        <div class="h-full w-full flex items-center justify-center bg-gray-100 text-gray-400 text-sm">Sem
                            capa</div>
                    @endif
                </div>

                @auth
                    @if (!auth()->user()->isAdmin() && $podeReview)
                        <button type="button"
                            class="mt-4 w-full rounded-md border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-slate-700 uppercase tracking-wider transition hover:bg-slate-50"
                            onclick="document.getElementById('review-modal').classList.remove('hidden')">
                            Avaliar
                        </button>
                    @endif
                @endauth
            </aside>

            <main>
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-slate-800">
                        {{ $livro->nome }}
                    </h1>
                        @if ($livro->autores->isNotEmpty())
                            <div class="mt-1 text-2xl font-light text-slate-600">
                                de {{ $livro->autores->first()->nome }}
                            </div>
                        @endif
                    <div class="text-sm text-gray-800 space-y-1 mt-6">
                        <div>
                            <span class="uppercase tracking-wider"><strong>ISBN:</strong></span>
                            <span class="ml-1">{{ $livro->isbn }}</span>
                        </div>

                        <div>
                            <span class="uppercase tracking-wider"><strong>Editora:</strong></span>
                            <span class="ml-1">{{ $livro->editora?->nome ?? '-' }}</span>
                        </div>

                        <div class="pt-4 inline-flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-5 {{ $livro->isDisponivel() ? 'text-emerald-500' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5 5.754 5 4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18c1.746 0 3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            @if ($livro->isDisponivel())
                                <span class="font-semibold text-emerald-500">Disponível</span>
                            @else
                                <span class="font-semibold text-slate-400 line-through">Indisponível</span>
                            @endif
                        </div>

                        <div class="mt-6 flex justify-end pb-2 text-right">
                            <div class="flex items-baseline gap-2">
                                <span class="text-lg text-slate-500 line-through">
                                    {{ number_format($precoAntigoDetalhe, 2, ',', '.') }} €
                                </span>
                                <span class="ml-1 text-3xl font-extrabold text-red-600">
                                    {{ number_format($precoAtualDetalhe, 2, ',', '.') }} €
                                </span>
                            </div>
                        </div>

                        @auth
                            @if (!auth()->user()->isAdmin())
                                <div class="mt-6 ml-auto w-full max-w-[19rem] space-y-3 text-right pb-8">
                                    @if ($livro->isDisponivel())
                                        <form method="POST" action="{{ route('carrinho.add', $livro->id) }}">
                                            @csrf
                                            <button type="submit" {{ $livroNoCarrinho ? 'disabled' : '' }}
                                                class="w-full rounded-md px-4 py-2.5 text-sm border font-semibold uppercase tracking-wider transition {{ $livroNoCarrinho ? 'cursor-not-allowed border-slate-3 bg-slate-100 text-slate-400' : 'bg-blue-800 text-white text-slate-800 hover:bg-blue-900' }}">
                                                {{ $livroNoCarrinho ? 'Adicionado ao carrinho' : 'Adicionar ao carrinho' }}
                                            </button>
                                        </form>
                                    @endif

                                    <div
                                        class="grid {{ $livro->isDisponivel() ? 'grid-cols-1' : 'grid-cols-[minmax(0,1fr)_52px]' }} gap-3 items-stretch">
                                        @if ($livro->isDisponivel())
                                            <form method="POST" action="{{ route('requisicoes.store') }}"
                                                onsubmit="return abrirPopupRequisicao(this)">
                                                @csrf
                                                <input type="hidden" name="livro_id" value="{{ $livro->id }}">
                                                <button type="submit"
                                                    class="w-full rounded-md border border-slate-400 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 uppercase tracking-wider transition hover:bg-slate-50">
                                                    Requisitar
                                                </button>
                                            </form>
                                        @else
                                            <div
                                                class="rounded-md border border-slate-200 bg-slate-50 text-slate-300 uppercase tracking-wider px-4 py-2.5 text-center text-sm font-medium">
                                                {{ $requisitadoPorMim ? 'Requisitado' : 'Indisponível' }}
                                            </div>
                                            <form method="POST" action="{{ route('livros.alerta', $livro->id) }}"
                                                class="flex h-full items-center justify-center">
                                                @csrf
                                                <button type="submit"
                                                    class="inline-flex h-full w-full items-center justify-center transition {{ $alertaAtivo ? 'text-blue-800' : 'text-slate-300' }}"
                                                    title="Avisar-me quando disponível">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        fill="{{ $alertaAtivo ? '#1E40AF' : '#ffffff' }}" viewBox="0 0 24 24"
                                                        stroke="{{ $alertaAtivo ? '#ffffff' : '#64748b' }}" stroke-width="0.5"
                                                        class="w-8 h-8">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9a6 6 0 1 0-12 0v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        @endauth
                        <div class="border-t border-slate-200 my-8"></div>
                        <div class="pt-4 text-gray-800 text-sm">
                            <span class="uppercase tracking-wider"><strong>Bibliografia:</strong></span>
                            @if (filled($livro->bibliografia))
                                @php
                                    $bibliografiaTexto = trim((string) $livro->bibliografia);
                                    $paragrafosBibliografia = preg_split('/\R{2,}/u', $bibliografiaTexto) ?: [];
                                @endphp

                                <div class="mt-2 space-y-4 text-justify leading-6 text-[15px] text-slate-800">
                                    @foreach ($paragrafosBibliografia as $paragrafo)
                                        <p>{!! nl2br(e(trim($paragrafo))) !!}</p>
                                    @endforeach
                                </div>
                            @else
                                <div class="mt-1">-</div>
                            @endif
                        </div>
                    </div>
                    <div class="lg:hidden"></div>
                </main>
            </div>

            {{-- Popup de Requisitar --}}
            <div id="requisicao-modal" class="hidden fixed inset-0 z-50 flex items-start justify-center pt-[18vh]">
                <div class="absolute inset-0 bg-black/50" onclick="fecharPopupRequisicao()"></div>
                <div class="relative w-[92%] max-w-xl rounded-xl border border-slate-200 bg-white p-6 shadow-xl">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base font-semibold text-slate-800">Confirmar requisição</h3>
                        <button type="button" class="text-slate-500 hover:text-slate-700 text-2xl leading-none"
                            onclick="fecharPopupRequisicao()">&times;</button>
                    </div>
                    <p class="text-sm text-slate-600">Tem a certeza que quer requisitar este livro?</p>
                    <div class="mt-5 flex justify-end gap-2">
                        <button type="button"
                            class="rounded-md border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50"
                            onclick="fecharPopupRequisicao()">
                            Cancelar
                        </button>
                        <button type="button"
                            class="rounded-md bg-blue-800 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-900"
                            onclick="confirmarRequisicao()">
                            Confirmar
                        </button>
                    </div>
                </div>
            </div>

            {{-- DIVIDER 1 --}}
            <div class="border-t border-slate-200 my-8"></div>

            {{-- SEÇÃO 2: Avaliações (Reviews) --}}
            <div class="py-8">
                @php
                    $reviewsAtivos = $livro->reviews->where('estado', 'ativo');
                    $totalReviews = $reviewsAtivos->count();
                    $mediaRating = $totalReviews > 0 ? $reviewsAtivos->avg('rating') : 0;
                    $starsCheia = floor($mediaRating);
                    $temMeiaEstrela = $mediaRating - $starsCheia >= 0.5;
                @endphp

                <div class="flex items-center gap-3 pb-4">
                    <h1 class="text-2xl font-bold text-slate-800">Avaliações</h1>
                    <span class="text-xl text-gray-800 font-semibold">|</span>
                    <div class="flex items-center gap-1">
                        <span
                            class="text-xl font-bold text-gray-800">{{ number_format($mediaRating, 1, ',', '.') }}</span>
                        <span class="text-yellow-500 text-lg">
                            {{ str_repeat('★', $starsCheia) }}@if ($temMeiaEstrela)½@endif{{ str_repeat('☆', max(0, 5 - $starsCheia - ($temMeiaEstrela ? 1 : 0))) }}
                        </span>
                    </div>
                    <span class="text-slate-500 text-sm">{{ $totalReviews }} @if ($totalReviews == 1)
                            avaliação
                        @else
                            avaliações
                        @endif
                    </span>
                </div>

                <div class="space-y-4">
                    @forelse ($reviewsAtivos as $review)
                        <div class="overflow-x-auto rounded-lg border border-slate-200 bg-white shadow-sm">
                            <div class="p-4 bg-white">
                                <div class="flex items-center gap-3 mb-2">
                                    <img src="{{ $review->user->profile_photo_url }}"
                                        alt="Foto de {{ $review->user->name }}"
                                        class="w-10 h-10 rounded-full object-cover border border-slate-200">
                                    <div class="flex-1">
                                        <p class="text-slate-700 text-sm font-semibold">{{ $review->user->name }}</p>
                                        <p class="text-yellow-500 text-sm">
                                            {{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}
                                        </p>
                                    </div>
                                </div>
                                <div class="text-gray-700 text-sm mt-3">{{ $review->comentario }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-slate-500">
                            <p class="text-sm text-left">Ainda não existem avaliações para este livro.</p>
                        </div>
                    @endforelse
                </div>

                @if ($podeReview)
                    <div id="review-modal" class="hidden fixed inset-0 z-50 flex items-start justify-center pt-[18vh]">
                        <div class="absolute inset-0 bg-black/50"
                            onclick="document.getElementById('review-modal').classList.add('hidden')"></div>
                        <div class="relative w-[92%] max-w-xl rounded-xl border border-slate-200 bg-white p-6 shadow-xl">
                            <div class="mb-4 flex items-center justify-between">
                                <h3 class="text-base font-semibold text-slate-800">Avaliar livro</h3>
                                <button type="button" class="text-slate-500 hover:text-slate-700 text-2xl leading-none"
                                    onclick="document.getElementById('review-modal').classList.add('hidden')">&times;</button>
                            </div>

                            <form method="POST" action="{{ route('reviews.store', $livro->id) }}">
                                @csrf
                                <label class="block">
                                    <span
                                        class="text-slate-700 text-xs font-semibold uppercase tracking-wider">Avaliação</span>
                                    <div class="mt-3 mb-6 flex items-center gap-2">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <label class="cursor-pointer"
                                                onmouseenter="previewEstrelasReview({{ $i }})"
                                                onmouseleave="limparPreviewEstrelasReview()">
                                                <input type="radio" name="rating" value="{{ $i }}"
                                                    class="sr-only" required onchange="atualizarEstrelasReview()">
                                                <span class="review-star text-2xl text-gray-300 transition-colors"
                                                    data-star="{{ $i }}">★</span>
                                            </label>
                                        @endfor
                                    </div>
                                </label>

                                <label class="block">
                                    <span
                                        class="text-slate-700 text-xs font-semibold uppercase tracking-wider">Comentário</span>
                                    <textarea name="comentario" rows="2"
                                        class="mt-2 w-full rounded-md border border-gray-300 focus:border-gray-500 focus:ring-gray-500 text-gray-700 p-2"></textarea>
                                </label>

                                <span class="text-xs text-slate-500">
                                    *A sua avaliação ficará visível para outros utilizadores após revisão pela equipa da
                                    biblioteca.

                                    <div class="mt-5 flex justify-end gap-2">
                                        <button type="button"
                                            class="rounded-md border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50"
                                            onclick="document.getElementById('review-modal').classList.add('hidden')">
                                            Cancelar
                                        </button>
                                        <button type="submit"
                                            class="rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-red-700">
                                            Confirmar
                                        </button>
                                    </div>
                            </form>
                        </div>
                    </div>
                @endif
            </div>

            {{-- DIVIDER 2 --}}
            <div class="border-t border-slate-200 my-8"></div>

            {{-- SEÇÃO 3: Livros Relacionados --}}
            @if ($relacionados && $relacionados->count())
                <div class="py-8">
                    <h1 class="text-2xl font-bold text-slate-800 pb-4">Livros Relacionados</h1>
                    <div class="grid grid-cols-2 gap-4 pb-2 sm:grid-cols-4 lg:grid-cols-6">
                        @foreach ($relacionados as $rel)
                            @php
                                $precoAtual = (float) $rel->preco;
                                $precoAntigo = $precoAtual > 0 ? $precoAtual / 0.9 : 0;
                                $autorRelacionado = $rel->autores->first()->nome ?? 'Autor desconhecido';
                            @endphp
                            <a href="{{ route('livros.show', $rel->id) }}"
                                class="group min-w-0 flex flex-col transition hover:opacity-90">
                                @if ($rel->imagem_capa)
                                    <div class="relative w-full overflow-hidden bg-white">
                                        <span class="absolute left-2 top-2 z-10 rounded-full bg-red-600 px-1.5 py-0.5 text-[10px] font-bold text-white">-10%</span>
                                        <img src="{{ str_starts_with($rel->imagem_capa, 'http') ? $rel->imagem_capa : asset('storage/' . $rel->imagem_capa) }}"
                                            alt="Capa de {{ $rel->nome }}"
                                            class="h-64 w-full object-cover" />
                                    </div>
                                @else
                                    <div class="flex h-64 w-full items-center justify-center rounded-md bg-slate-100 text-xs text-gray-400">Sem capa</div>
                                @endif

                                <div class="pt-1.5">
                                    <h3 class="line-clamp-2 text-[15px] font-semibold leading-snug text-slate-800">
                                        {{ $rel->nome }}
                                    </h3>
                                    <p class="mt-0.5 text-[12px] text-slate-600">
                                        de {{ $autorRelacionado }}
                                    </p>
                                    <div class="mt-1 flex items-baseline gap-1">
                                        <span class="text-md font-bold text-red-600">
                                            {{ number_format($precoAtual, 2, ',', '.') }} €
                                        </span>
                                        <span class="text-[12px] text-slate-500 line-through">
                                            {{ number_format($precoAntigo, 2, ',', '.') }} €
                                        </span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- DIVIDER 3 --}}
            <div class="border-t border-slate-200 my-8"></div>

            {{-- SEÇÃO 4: Histórico de Requisições --}}
            <div class="py-8">
                <h1 class="text-2xl font-bold text-slate-800 pb-4">Histórico de Requisições</h1>
                <div class="overflow-x-auto rounded-lg border border-slate-200 bg-white shadow-sm">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead>
                            <tr class="bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500">
                                <th class="px-4 py-3">Nº</th>
                                <th class="px-4 py-3">Utilizador</th>
                                <th class="px-4 py-3">Requisitado em</th>
                                <th class="px-4 py-3">Devolvido em</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse ($historico as $req)
                                <tr>
                                    <td class="px-4 py-3">{{ $req->numero ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ $req->user->name ?? '-' }}</td>
                                    <td class="px-4 py-3">
                                        {{ $req->requisitado_em ? \Carbon\Carbon::parse($req->requisitado_em)->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        {{ $req->devolvido_em ? \Carbon\Carbon::parse($req->devolvido_em)->format('d/m/Y') : '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-sm text-slate-500">Sem histórico
                                        de requisições.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <script>
                let formularioRequisicaoPendente = null;

                function abrirPopupRequisicao(form) {
                    if (form.dataset.confirmadoRequisicao === '1') {
                        return true;
                    }

                    formularioRequisicaoPendente = form;
                    document.getElementById('requisicao-modal').classList.remove('hidden');
                    return false;
                }

                function fecharPopupRequisicao() {
                    document.getElementById('requisicao-modal').classList.add('hidden');
                    formularioRequisicaoPendente = null;
                }

                function confirmarRequisicao() {
                    if (!formularioRequisicaoPendente) {
                        return;
                    }

                    formularioRequisicaoPendente.dataset.confirmadoRequisicao = '1';
                    formularioRequisicaoPendente.submit();
                }

                let previewRatingReview = null;

                function renderEstrelasReview(valorAtivo) {
                    const selecionado = document.querySelector('input[name="rating"]:checked');
                    const valorSelecionado = selecionado ? Number(selecionado.value) : 0;
                    const ratingAtual = valorAtivo ?? valorSelecionado;
                    const estrelas = document.querySelectorAll('.review-star');

                    estrelas.forEach((estrela) => {
                        const valorEstrela = Number(estrela.dataset.star);
                        const ativa = valorEstrela <= ratingAtual;

                        estrela.classList.toggle('text-yellow-500', ativa);
                        estrela.classList.toggle('text-gray-300', !ativa);
                    });
                }

                function atualizarEstrelasReview() {
                    renderEstrelasReview(previewRatingReview);
                }

                function previewEstrelasReview(valor) {
                    previewRatingReview = Number(valor);
                    renderEstrelasReview(previewRatingReview);
                }

                function limparPreviewEstrelasReview() {
                    previewRatingReview = null;
                    renderEstrelasReview();
                }

                atualizarEstrelasReview();
            </script>
        </div>
    </div>
</x-app-layout>

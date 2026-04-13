<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900">Detalhes do livro</h1>
            </div>
        </div>
    </x-slot>
    <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
        @php
            $precoAtualDetalhe = (float) $livro->preco;
            $precoAntigoDetalhe = $precoAtualDetalhe > 0 ? $precoAtualDetalhe / 0.9 : 0;
            $bibliografiaTexto = trim((string) ($livro->bibliografia ?? ''));
            $paragrafosBibliografia = filled($bibliografiaTexto) ? preg_split('/\R{2,}/u', $bibliografiaTexto) : [];
        @endphp

        <div class="mb-6 flex items-center gap-2 text-sm">
            <a href="{{ route('admin.livros') }}" class="inline-flex items-center gap-1 text-slate-800">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" class="h-4 w-4 shrink-0" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12 11.204 3.046a1.125 1.125 0 0 1 1.592 0L21.75 12M4.5 9.75V19.5A1.5 1.5 0 0 0 6 21h4.5v-5.25a1.5 1.5 0 0 1 1.5-1.5h0a1.5 1.5 0 0 1 1.5 1.5V21H18a1.5 1.5 0 0 0 1.5-1.5V9.75" />
                </svg>
                <span class="ml-2 text-xs underline underline-offset-2">Livros</span>
            </a>
            <span class="text-xs text-slate-400">/</span>
            <span class="text-xs text-slate-500">{{ $livro->nome }}</span>
        </div>

        <div class="grid items-start gap-8 lg:grid-cols-[18rem_minmax(0,1fr)]">
            <aside class="self-start lg:sticky lg:top-6">
                <div class="relative h-[29rem] w-full overflow-hidden shadow-sm">
                    @if ($livro->imagem_capa)
                        <span class="absolute left-3 top-3 z-10 rounded-full bg-red-600 px-2 py-1 text-[12px] font-bold text-white">-10%</span>
                        <img src="{{ str_starts_with($livro->imagem_capa, 'http') ? $livro->imagem_capa : asset('storage/' . $livro->imagem_capa) }}"
                            alt="Capa de {{ $livro->nome }}" class="h-full w-full object-cover" />
                    @else
                        <div class="flex h-full w-full items-center justify-center bg-gray-100 text-sm text-gray-400">Sem capa</div>
                    @endif
                </div>
            </aside>

            <main>
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-slate-800">{{ $livro->nome }}</h1>

                    @if ($livro->autores->isNotEmpty())
                        <div class="mt-1 text-2xl font-light text-slate-600">de {{ $livro->autores->first()->nome }}</div>
                    @endif

                    <div class="mt-6 space-y-1 text-sm text-gray-800">
                        <div>
                            <span class="uppercase tracking-wider"><strong>ISBN:</strong></span>
                            <span class="ml-1">{{ $livro->isbn }}</span>
                        </div>

                        <div>
                            <span class="uppercase tracking-wider"><strong>Autor(es):</strong></span>
                            <span class="ml-1">
                                @forelse ($livro->autores as $autor)
                                    <span>{{ $autor->nome }}@if (! $loop->last), @endif</span>
                                @empty
                                    -
                                @endforelse
                            </span>
                        </div>

                        <div>
                            <span class="uppercase tracking-wider"><strong>Editora:</strong></span>
                            <span class="ml-1">{{ $livro->editora?->nome ?? '-' }}</span>
                        </div>

                        <div class="inline-flex items-center gap-2 pt-4">
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
                                <span class="text-lg text-slate-500 line-through">{{ number_format($precoAntigoDetalhe, 2, ',', '.') }} €</span>
                                <span class="ml-1 text-3xl font-extrabold text-red-600">{{ number_format($precoAtualDetalhe, 2, ',', '.') }} €</span>
                            </div>
                        </div>

                        <div class="ml-auto mt-6 w-full max-w-[19rem] space-y-3 pb-8 text-right">
                            @if ($livro->isDisponivel())
                                <form method="POST" action="{{ route('requisicoes.store') }}">
                                    @csrf
                                    <input type="hidden" name="livro_id" value="{{ $livro->id }}">
                                    <button type="submit"
                                        class="w-full rounded-md border border-slate-400 bg-white px-4 py-2.5 text-sm font-semibold uppercase tracking-wider text-slate-700 transition hover:bg-slate-50">
                                        Requisitar
                                    </button>
                                </form>
                            @else
                                <div class="rounded-md border border-slate-200 bg-slate-50 px-4 py-2.5 text-center text-sm font-medium uppercase tracking-wider text-slate-300">
                                    Indisponível
                                </div>
                            @endif
                        </div>

                        <div class="my-8 border-t border-slate-200"></div>

                        <div class="pt-4 text-sm text-gray-800">
                            <span class="uppercase tracking-wider"><strong>Bibliografia:</strong></span>
                            @if (filled($bibliografiaTexto))
                                <div class="mt-2 space-y-4 text-[15px] leading-6 text-slate-800 text-justify">
                                    @foreach ($paragrafosBibliografia as $paragrafo)
                                        <p>{!! nl2br(e(trim($paragrafo))) !!}</p>
                                    @endforeach
                                </div>
                            @else
                                <div class="mt-1">-</div>
                            @endif
                        </div>
                    </div>
                </div>
            </main>
        </div>

        <div class="my-8 border-t border-slate-200"></div>

        @php
            $reviewsAtivos = $livro->reviews->where('estado', 'ativo');
            $totalReviews = $reviewsAtivos->count();
            $mediaRating = $totalReviews > 0 ? $reviewsAtivos->avg('rating') : 0;
            $starsCheia = floor($mediaRating);
            $temMeiaEstrela = $mediaRating - $starsCheia >= 0.5;
        @endphp

        <div class="py-8">
            <div class="flex items-center gap-3 pb-4">
                <h2 class="text-2xl font-semibold text-slate-800">Avaliações</h2>
                <span class="text-xl font-semibold text-gray-800">|</span>
                <div class="flex items-center gap-1">
                    <span class="text-xl font-bold text-gray-800">{{ number_format($mediaRating, 1, ',', '.') }}</span>
                    <span class="text-lg text-yellow-500">
                        {{ str_repeat('★', $starsCheia) }}@if ($temMeiaEstrela)½@endif{{ str_repeat('☆', max(0, 5 - $starsCheia - ($temMeiaEstrela ? 1 : 0))) }}
                    </span>
                </div>
                <span class="text-sm text-slate-500">{{ $totalReviews }} {{ $totalReviews === 1 ? 'avaliação' : 'avaliações' }}</span>
            </div>
            <div class="space-y-4">
                @forelse ($reviewsAtivos as $review)
                    <div class="overflow-x-auto rounded-lg border border-slate-200 bg-white shadow-sm">
                        <div class="bg-white p-4">
                            <div class="mb-2 flex items-center gap-3">
                                <img src="{{ $review->user->profile_photo_url }}" alt="Foto de {{ $review->user->name }}"
                                    class="h-10 w-10 rounded-full border border-slate-200 object-cover">
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-slate-700">{{ $review->user->name }}</p>
                                    <p class="text-sm text-yellow-500">
                                        {{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}
                                    </p>
                                </div>
                            </div>
                            <div class="mt-3 text-sm text-gray-700">{{ $review->comentario }}</div>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-slate-500">
                        <p class="text-left text-sm">Ainda não existem avaliações para este livro.</p>
                    </div>
                @endforelse
            </div>
        </div>
        <div class="my-8 border-t border-slate-200"></div>

        <div class="mt-10">
            <h2 class="text-2xl font-semibold text-slate-800 pb-4">Histórico de Requisições</h2>
            <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm">
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
                        @forelse ($livro->requisicoes as $requisicao)
                            <tr>
                                <td class="px-4 py-2">{{ $requisicao->numero ?? '-' }}</td>
                                <td class="px-4 py-2">{{ $requisicao->user?->name ?? '-' }}</td>
                                <td class="px-4 py-2">{{ $requisicao->created_at?->format('d/m/Y') ?? '-' }}</td>
                                <td class="px-4 py-2">{{ $requisicao->devolvido_em?->format('d/m/Y') ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-2 text-center text-gray-400">Sem requisições</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-admin-layout>

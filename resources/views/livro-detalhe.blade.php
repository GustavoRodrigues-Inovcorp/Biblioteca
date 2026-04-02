<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
            {{ __('Detalhes do Livro') }}
        </h2>
    </x-slot>

    <div class="max-w-4xl mx-auto py-10">
        {{--
            Detalhes do livro, incluindo capa, título, autores, editora, preço e bibliografia.
            Se o livro estiver disponível, mostra botão para requisitar. --}}
            Se o utilizador tiver requisitado e devolvido o livro, mostra formulário para submeter um review.
            Mostra também uma lista de reviews ativos para o livro.
            Sugere livros relacionados com base em palavras comuns na bibliografia.
        --}}
        <div class="flex flex-col md:flex-row gap-8">
            <div class="flex-shrink-0">
                <div class="h-64 w-44 overflow-hidden rounded shadow bg-gray-100 flex items-center justify-center">
                    @if ($livro->imagem_capa)
                        <img src="{{ str_starts_with($livro->imagem_capa, 'http') ? $livro->imagem_capa : asset('storage/' . $livro->imagem_capa) }}"
                            alt="Capa de {{ $livro->nome }}" class="h-full w-full object-cover" />
                    @else
                        <span class="text-gray-400 text-xs">Sem capa</span>
                    @endif
                </div>
            </div>
            <div class="flex-1 space-y-2">
                <div class="flex items-center justify-between mb-2">
                    <h1 class="text-2xl font-bold text-slate-800">{{ $livro->nome }}</h1>
                    <div class="flex items-center gap-2">
                        @if (!$livro->isDisponivel())
                            @auth
                                @php
                                    $alertaAtivo = auth()->user()->alertasLivro->contains('livro_id', $livro->id);
                                @endphp
                                <form method="POST" action="{{ route('livros.alerta', $livro->id) }}">
                                    @csrf
                                    <button type="submit"
                                        class="w-8 h-8 flex items-center justify-center rounded-full border-2 transition hover:bg-slate-100"
                                        title="Avisar-me quando disponível">
                                        @if($alertaAtivo)
                                            <!-- Sino preenchido -->
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="#64748b" viewBox="0 0 24 24" stroke="#64748b" stroke-width="1.5" class="w-5 h-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9a6 6 0 1 0-12 0v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                                            </svg>
                                        @else
                                            <!-- Sino outline -->
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#64748b" stroke-width="1.5" class="w-5 h-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9a6 6 0 1 0-12 0v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                                            </svg>
                                        @endif
                                    </button>
                                </form>
                            @endauth
                        @endif
                        <div>
                            @if ($livro->isDisponivel())
                                @include('components.livros.requisitar-form', [
                                    'livro' => $livro,
                                    'livrosRequisitados' => $livrosRequisitados ?? 0,
                                ])
                            @else
                                <span class="inline-block px-4 py-2 rounded text-sm text-gray-500">Indisponível</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="flex flex-wrap gap-6 text-sm text-gray-500 mb-1 items-center">
                    <span class="uppercase tracking-wider"><strong>ISBN:</strong> {{ $livro->isbn }}</span>

                    <div class="flex flex-wrap gap-2 items-center">
                        <span class="uppercase tracking-wider"><strong>Editora:</strong></span>
                        <div>{{ $livro->editora?->nome ?? '-' }}</div>
                    </div>

                    <span class="uppercase tracking-wider"><strong>Preço:</strong> <span
                            class="font-semibold text-slate-700">{{ number_format((float) $livro->preco, 2, ',', '.') }}
                            EUR</span></span>
                </div>

                <div class="text-sm text-gray-500">
                    <span class="uppercase tracking-wider"><strong>Autor(es):</strong></span>
                    @forelse ($livro->autores as $autor)
                        <span class="ml-1">{{ $autor->nome }}</span>
                    @empty
                        <span class="ml-1">Sem autor</span>
                    @endforelse
                </div>

                <div class="mt-2 text-gray-500 text-sm">
                    <span class="uppercase tracking-wider"><strong>Bibliografia:</strong></span>
                    <div class="mt-1">{{ $livro->bibliografia ?? '-' }}</div>
                </div>
                <!-- Botão de requisitar movido para junto do título -->
            </div>
        </div>
        <div style="margin-top: 6rem;">
            <h2 class="pb-2 text-left text-sm font-semibold uppercase tracking-wider text-slate-500">Reviews</h2>
            @forelse ($livro->reviews->where('estado', 'ativo') as $review)
                <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm">
                    <div class="border rounded p-4 bg-gray-50">
                        <div class="flex items-center gap-2 mb-1">
                            <img src="{{ $review->user->profile_photo_url }}" alt="Foto de {{ $review->user->name }}"
                                class="w-8 h-8 rounded-full object-cover border border-slate-200">
                            <span class="text-slate-500 text-sm font-semibold">{{ $review->user->name }}</span>
                            <span
                                class="text-yellow-500">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</span>
                        </div>
                        <div class="text-gray-700 text-sm mt-2">{{ $review->comentario }}</div>
                    </div>
                </div>
            @empty
                <div class="text-xs text-slate-500">Ainda não existem reviews para este livro.</div>
            @endforelse

            {{-- 
                Formulário para submeter um review
                Apenas se o utilizador tiver requisitado e devolvido o livro
            --}}
            @if ($podeReview)
                <div class="mb-8">
                    <h3 class="pb-2 text-left text-sm font-semibold uppercase tracking-wider text-slate-500 mt-10">Deixe
                        o seu Review</h3>
                    <form method="POST" action="{{ route('reviews.store', $livro->id) }}">
                        @csrf
                        <label class="block">
                            <span class="text-slate-500 text-xs font-semibold uppercase tracking-wider">Avaliação</span>
                            <div x-data="{ rating: 0, hover: 0 }" class="flex items-center space-x-1 mt-2 mb-8">
                                <template x-for="star in 5" :key="star">
                                    <svg @click="rating = star" @mouseover="hover = star" @mouseleave="hover = 0"
                                        :class="[(hover >= star || (!hover && rating >= star)) ? 'text-yellow-500' :
                                            'text-gray-300', 'w-6 h-6 cursor-pointer transition-colors duration-150'
                                        ]"
                                        fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.967a1 1 0 00.95.69h4.175c.969 0 1.371 1.24.588 1.81l-3.382 2.46a1 1 0 00-.364 1.118l1.287 3.966c.3.921-.755 1.688-1.54 1.118l-3.382-2.46a1 1 0 00-1.176 0l-3.382 2.46c-.784.57-1.838-.197-1.54-1.118l1.287-3.966a1 1 0 00-.364-1.118L2.049 9.394c-.783-.57-.38-1.81.588-1.81h4.175a1 1 0 00.95-.69l1.286-3.967z" />
                                    </svg>
                                </template>
                                <input type="hidden" name="rating" x-model="rating" required>
                            </div>
                        </label>
                        <label class="block">
                            <span class="text-slate-500 text-xs font-semibold uppercase tracking-wider">Comentário</span>
                            <textarea name="comentario" rows="3" class="mt-1 w-full rounded-md border-gray-300 focus:border-gray-500 focus:ring-gray-500 text-gray-700"></textarea>
                        </label>
                        <x-button>Submeter Review</x-button>
                    </form>
                </div>
            @endif

            {{-- 
                Sugere livros relacionados com base em palavras comuns na bibliografia.
                Exclui o próprio livro.
            --}}
            @if ($relacionados && $relacionados->count())
                <div style="margin-top: 6rem;">
                    <h2 class="pb-2 text-left text-sm font-semibold uppercase tracking-wider text-slate-500 mt-10">
                        Livros Relacionados</h2>
                        <div class="w-full flex flex-row gap-6 pb-2 justify-between items-end">
                        @foreach ($relacionados as $rel)
                                <a href="{{ route('livros.show', $rel->id) }}"
                                    class="flex-1 max-w-[150px] min-w-0 flex flex-col items-center transition hover:scale-105">
                                    @if ($rel->imagem_capa)
                                        <div class="w-full h-[200px] aspect-[3/4] rounded-xl bg-white shadow-[4px_0_16px_-4px_#bbb,0_6px_16px_-4px_#bbb] flex items-end">
                                            <img src="{{ str_starts_with($rel->imagem_capa, 'http') ? $rel->imagem_capa : asset('storage/' . $rel->imagem_capa) }}"
                                                alt="Capa de {{ $rel->nome }}"
                                                class="w-full h-full rounded-md object-cover aspect-[3/4]" />
                                        </div>
                                    @else
                                        <span class="text-gray-400 text-xs">Sem capa</span>
                                    @endif
                                </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- 
                Tabela do histórico de requisições realizadas pelo utilizador.
                Mostra data de requisição e devolução.
                Se não houver histórico, mostra mensagem indicativa.
            --}}
            <h2 class="pb-2 text-left text-sm font-semibold uppercase tracking-wider text-slate-500" style="margin-top: 6rem;">Histórico de
                Requisições</h2>
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
                                <td colspan="4" class="px-4 py-8 text-center text-xs text-slate-500">Sem histórico de
                                    requisições.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>

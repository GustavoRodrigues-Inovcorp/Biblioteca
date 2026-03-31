<div>
    <div class="mb-2">
        <h2 class="pb-2 text-left text-sm font-semibold uppercase tracking-wider text-slate-500 mt-10">Reviews Submetidos
        </h2>
        <div class="flex flex-wrap gap-6 items-end pt-4 bg-slate-50 rounded-lg px-4 py-3 border border-slate-200">
            <div>
                <label class="block text-xs font-semibold mb-1 uppercase tracking-wider text-slate-700">Estado</label>
                <div class="flex gap-2">
                    <select wire:model.live="estado"
                        class="rounded-lg border border-slate-300 px-3 py-2 text-sm bg-white shadow-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition text-black w-[180px]">
                        <option value="" style="color:#222">Todos</option>
                        <option value="requisitado" style="color:#222">Suspenso</option>
                        <option value="ativo" style="color:#222">Ativo</option>
                        <option value="recusado" style="color:#222">Recusado</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto rounded-xl bg-white shadow mb-20">
        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead>
                    <tr class="bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500">
                        <th class="px-4 py-3 bg-slate-50 text-slate-500">Livro</th>
                        <th class="px-4 py-3 bg-slate-50 text-slate-500">Utilizador</th>
                        <th class="px-4 py-3 bg-slate-50 text-slate-500">Avaliação</th>
                        <th class="px-4 py-3 bg-slate-50 text-slate-500">Comentário</th>
                        <th class="px-4 py-3 bg-slate-50 text-slate-500">Estado</th>
                        <th class="px-4 py-3 bg-slate-50 text-slate-500 text-right"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse ($reviews as $review)
                        <tr class="align-middle">
                            <td class="px-4 py-3 min-w-56 align-middle">
                                <div class="flex gap-3 items-center">
                                    <div class="avatar">
                                        <div class="h-12 w-8 overflow-hidden rounded shadow-sm ring-1 ring-black/5">
                                            @if ($review->livro->imagem_capa)
                                                <img src="{{ str_starts_with($review->livro->imagem_capa, 'http') ? $review->livro->imagem_capa : asset('storage/' . $review->livro->imagem_capa) }}"
                                                    alt="Capa" class="h-full w-full object-cover" />
                                            @else
                                                <div
                                                    class="flex h-full w-full items-center justify-center bg-gray-200 text-xs">
                                                    -</div>
                                            @endif
                                        </div>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-slate-900 text-sm">{{ $review->livro->nome }}
                                        </div>
                                        <div class="text-xs text-slate-500 mt-1">ISBN: {{ $review->livro->isbn }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap align-middle min-w-[180px]">
                                @if ($review->user)
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $review->user->profile_photo_url }}" alt="Foto"
                                            class="h-9 w-9 rounded-full object-cover border border-slate-200 shadow-sm bg-white" />
                                        <div class="flex flex-col">
                                            <span
                                                class="font-semibold text-slate-900 text-xs">{{ $review->user->name }}</span>
                                            <span class="text-xs text-slate-500">{{ $review->user->email }}</span>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex items-center">
                                    <span
                                        class="pl-4 py-3 text-xs align-middle font-bold mr-1">{{ $review->rating ?? '-' }}</span>
                                    <span class="text-yellow-500">★</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-xs align-middle">
                                {{ $review->comentario ?? '-' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-xs align-middle">
                                @php
                                    $estado = $review->estado ?? '';
                                @endphp
                                <div class="flex items-center justify-between w-full gap-2">
                                    <div>
                                        @if ($estado === 'suspenso')
                                            <span
                                                class="inline-block px-2 py-0.5 rounded bg-slate-100 text-slate-800 text-[11px] font-semibold align-middle">Suspenso</span>
                                        @elseif ($estado === 'ativo')
                                            <span
                                                class="inline-block px-2 py-0.5 rounded bg-green-100 text-green-800 text-[11px] font-semibold align-middle">Ativo</span>
                                        @elseif ($estado === 'recusado')
                                            <span
                                                class="inline-block px-2 py-0.5 rounded bg-red-100 text-red-800 text-[11px] font-semibold align-middle">Recusado</span>
                                        @else
                                            <span
                                                class="inline-block px-2 py-0.5 rounded bg-blue-100 text-blue-800 text-[11px] font-semibold align-middle">Sem
                                                Review</span>
                                        @endif
                                    </div>
                                    <div class="flex items-center ml-auto">
                                        @if ($estado === 'suspenso')
                                            <button type="button"
                                                wire:click="confirmarAcao({{ $review->id }}, 'ativo')"
                                                class="inline-flex items-center rounded-md border border-green-200 px-3 py-1.5 text-xs font-medium text-green-700 transition hover:bg-green-50 ml-2">Ativar</button>
                                            <button type="button"
                                                wire:click="confirmarAcao({{ $review->id }}, 'recusado')"
                                                class="inline-flex items-center rounded-md border border-red-200 px-3 py-1.5 text-xs font-medium text-red-700 transition hover:bg-red-50 ml-1">Recusar</button>
                                        @endif
                                        @if ($showPopup)
                                            <div class="fixed top-0 left-0 w-full h-full flex items-center justify-center z-50"
                                                style="background:rgba(0,0,0,0.20);">
                                                <div
                                                    class="relative bg-white rounded-lg shadow-xl border {{ $popupAcao === 'ativo' ? 'border-green-400' : 'border-red-400' }} p-6 max-w-md w-full animate-fade-in">
                                                    <div class="flex items-center mb-4">
                                                        <svg class="w-8 h-8 {{ $popupAcao === 'ativo' ? 'text-green-500' : 'text-red-500' }} mr-2"
                                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        <span
                                                            class="text-lg font-semibold {{ $popupAcao === 'ativo' ? 'text-green-600' : 'text-red-600' }}">
                                                            {{ $popupAcao === 'ativo' ? 'Confirmação' : 'Confirmação' }}
                                                        </span>
                                                    </div>
                                                    <div
                                                        class="mb-6 text-center break-words leading-relaxed max-w-[90%] mx-auto {{ $popupAcao === 'ativo' ? 'text-green-700' : 'text-red-700' }}">
                                                        @if ($popupAcao === 'ativo')
                                                            Tens a certeza que queres ativar esta review?
                                                        @elseif($popupAcao === 'recusado')
                                                            Tens a certeza que queres recusar esta review?
                                                            <div class="mt-4 text-left">
                                                                <label
                                                                    class="block text-xs font-semibold text-red-700 mb-1">Justificação</label>
                                                                <textarea wire:model.defer="justificacao" rows="2"
                                                                    class="w-full rounded border border-red-300 focus:border-red-500 focus:ring-0 text-xs text-slate-500"></textarea>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="flex justify-center gap-4">
                                                        <button wire:click="executarAcao"
                                                            class="{{ $popupAcao === 'ativo' ? 'bg-green-500 hover:bg-green-600' : 'bg-red-500 hover:bg-red-600' }} text-white font-medium px-4 py-2 rounded transition min-w-[100px] text-xs">
                                                            {{ $popupAcao === 'ativo' ? 'Confirmar' : 'Confirmar' }}
                                                        </button>
                                                        <button wire:click="cancelarPopup"
                                                            class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium px-4 py-2 rounded transition min-w-[100px] text-xs">Cancelar</button>
                                                    </div>
                                                </div>
                                                <style>
                                                    .animate-fade-in {
                                                        animation: fadeIn .3s ease;
                                                    }

                                                    @keyframes fadeIn {
                                                        from {
                                                            opacity: 0;
                                                            transform: scale(0.95);
                                                        }

                                                        to {
                                                            opacity: 1;
                                                            transform: scale(1);
                                                        }
                                                    }
                                                </style>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-xs text-slate-500">Nenhuma review
                                encontrada.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

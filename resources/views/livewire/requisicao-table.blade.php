<div>
    <div>
        {{-- Popup de review após devolução --}}
        @if ($mostrarPopupReview)
            <div class="fixed top-0 left-0 w-full h-full flex items-center justify-center z-50">
                <div class="absolute inset-0 bg-black bg-opacity-60"></div>
                <div
                    class="relative bg-white rounded-lg shadow-xl border-2 border-yellow-400 p-6 max-w-xl w-full animate-fade-in">
                    <div class="flex items-center mb-4">
                        <svg class="w-8 h-8 text-yellow-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-lg font-bold text-yellow-600">Avaliação</span>
                    </div>
                    <form wire:submit.prevent="submeterReview" class="space-y-6">
                        <div>
                            <div x-data="{ rating: $wire.entangle('reviewRating'), hover: 0 }" class="flex items-center justify-center space-x-1 mt-2 mb-8">
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
                            </div>
                            @error('reviewRating')
                                <span class="text-xs text-red-500">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <span class="block text-xs font-semibold text-yellow-700">Comentário</span>
                            <div class="text-left">
                                <textarea wire:model.defer="reviewComentario" name="comentario" rows="2"
                                    class="mt-1 w-full rounded-md border-gray-300 focus:border-yellow-500 focus:ring-yellow-500 text-gray-700"></textarea>
                                @error('reviewComentario')
                                    <span class="text-xs text-red-500">{{ $message }}</span>
                                @enderror
                                <span class="text-xs text-slate-500">*A avaliação irá ser analisada pela administração.</span>
                            </div>
                        </div>
                        <div class="flex justify-center gap-4 mt-6">
                            <button type="submit"
                                class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold px-8 py-2 rounded transition min-w-[100px] text-xs">OK</button>
                            <button type="button" wire:click="$set('mostrarPopupReview', false)"
                                class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium px-4 py-2 rounded transition min-w-[100px] text-xs">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
        {{-- Popup de confirmação de devolução --}}
        @if ($mostrarPopupDevolucao)
            <div id="confirmar-devolucao-popup"
                class="fixed top-0 left-0 w-full h-full flex items-center justify-center z-50" style="display:flex;">
                <div class="absolute inset-0 bg-black bg-opacity-60"></div>
                <div
                    class="relative bg-white rounded-lg shadow-xl border border-blue-400 p-6 max-w-md w-full animate-fade-in">
                    <div class="flex items-center mb-4">
                        <svg class="w-8 h-8 text-blue-500 mr-2" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-lg font-semibold text-blue-600">Pedir devolução?</span>
                    </div>
                    <div class="text-blue-700 mb-6 text-center break-words leading-relaxed max-w-[90%] mx-auto text-xs">
                        Queres mesmo pedir a devolução deste livro? O pedido será enviado para a administração.</div>
                    <div class="flex justify-center gap-4">
                        <button wire:click="confirmarDevolucao"
                            class="bg-blue-500 hover:bg-blue-600 text-white font-medium px-4 py-2 rounded transition min-w-[100px] text-xs">Sim,
                            pedir devolução</button>
                        <button wire:click="$set('mostrarPopupDevolucao', false)"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium px-4 py-2 rounded transition min-w-[100px] text-xs">Cancelar</button>
                    </div>
                </div>
            </div>
        @endif

        {{-- Popup de confirmação de aceitar --}}
        @if ($mostrarPopupAceitar)
            <div id="confirmar-aceitar-popup"
                class="fixed top-0 left-0 w-full h-full flex items-center justify-center z-50" style="display:flex;">
                <div class="absolute inset-0 bg-black bg-opacity-60"></div>
                <div
                    class="relative bg-white rounded-lg shadow-xl border border-green-400 p-6 max-w-md w-full animate-fade-in">
                    <div class="flex items-center mb-4">
                        <svg class="w-8 h-8 text-green-500 mr-2" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-lg font-semibold text-green-600">Aceitar requisição?</span>
                    </div>
                    <div
                        class="text-green-700 mb-6 text-center break-words leading-relaxed max-w-[90%] mx-auto text-xs">
                        Queres mesmo aceitar esta requisição?</div>
                    <div class="flex justify-center gap-4">
                        <button wire:click="confirmarAceitarRequisicao"
                            class="bg-green-500 hover:bg-green-600 text-white font-medium px-4 py-2 rounded transition min-w-[100px] text-xs">Sim,
                            aceitar</button>
                        <button wire:click="$set('mostrarPopupAceitar', false)"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium px-4 py-2 rounded transition min-w-[100px] text-xs">Cancelar</button>
                    </div>
                </div>
            </div>
        @endif

        {{-- Popup de confirmação de recusar --}}
        @if ($mostrarPopupRecusar)
            <div id="confirmar-recusar-popup"
                class="fixed top-0 left-0 w-full h-full flex items-center justify-center z-50" style="display:flex;">
                <div class="absolute inset-0 bg-black bg-opacity-60"></div>
                <div
                    class="relative bg-white rounded-lg shadow-xl border border-red-400 p-6 max-w-md w-full animate-fade-in">
                    <div class="flex items-center mb-4">
                        <svg class="w-8 h-8 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-lg font-semibold text-red-600">Recusar requisição?</span>
                    </div>
                    <div class="text-red-700 mb-6 text-center break-words leading-relaxed max-w-[90%] mx-auto text-xs">
                        Queres mesmo recusar esta requisição?</div>
                    <div class="flex justify-center gap-4">
                        <button wire:click="confirmarRecusarRequisicao"
                            class="bg-red-500 hover:bg-red-600 text-white font-medium px-4 py-2 rounded transition min-w-[100px] text-xs">Sim,
                            recusar</button>
                        <button wire:click="$set('mostrarPopupRecusar', false)"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium px-4 py-2 rounded transition min-w-[100px] text-xs">Cancelar</button>
                    </div>
                </div>
            </div>
        @endif

        {{-- Tabela - As minhas requisições --}}
        <span class="pb-2 text-left text-sm font-semibold uppercase tracking-wider text-slate-500">As minhas
            requisições</span>
        <div class="flex flex-wrap gap-4 mb-6 mt-2">
            <div
                class="flex-1 min-w-[180px] bg-white rounded-lg shadow border border-slate-200 p-4 flex flex-col items-center">
                <span
                    class="block text-[13px] text-blue-900/60 font-semibold uppercase tracking-wider text-center mb-1">Requisições
                    Ativas</span>
                <span
                    class="text-2xl font-bold text-slate-600 mt-1">{{ $minhasRequisicoes->where('devolvido_em', null)->count() }}</span>
            </div>
            <div
                class="flex-1 min-w-[180px] bg-white rounded-lg shadow border border-slate-200 p-4 flex flex-col items-center">
                <span
                    class="block text-[13px] text-blue-900/60 font-semibold uppercase tracking-wider text-center mb-1">Últimos
                    30 dias</span>
                <span
                    class="text-2xl font-bold text-slate-600 mt-1">{{ $minhasRequisicoes->where('requisitado_em', '>=', now()->subDays(30))->count() }}</span>
            </div>
            <div
                class="flex-1 min-w-[180px] bg-white rounded-lg shadow border border-slate-200 p-4 flex flex-col items-center">
                <span
                    class="block text-[13px] text-blue-900/60 font-semibold uppercase tracking-wider text-center mb-1">Entregues
                    Hoje</span>
                <span class="text-2xl font-bold text-slate-600 mt-1">
                    {{ $minhasRequisicoes->filter(function ($req) {
                            if (!$req->devolvido_em) {
                                return false;
                            }
                            try {
                                $data = \Carbon\Carbon::parse($req->devolvido_em);
                                return $data->isToday();
                            } catch (\Exception $e) {
                                return false;
                            }
                        })->count() }}
                </span>
            </div>
        </div>
        <div class="mb-2">
            <div class="flex flex-wrap gap-6 items-end pt-4 bg-slate-50 rounded-lg px-4 py-3 border border-slate-200">
                <div>
                    <label
                        class="block text-xs font-semibold mb-1 uppercase tracking-wider text-slate-700">Estado</label>
                    <div class="flex gap-2">
                        @if ($isAdmin)
                            <select wire:model.live="estado"
                                class="rounded-lg border border-slate-300 px-3 py-2 text-sm bg-white shadow-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition text-black w-[180px]">
                                <option value="" style="color:#222">Todos</option>
                                <option value="requisitado" style="color:#222">Requisitados</option>
                                <option value="aceite" style="color:#222">Aceites</option>
                            </select>
                        @else
                            <select wire:model.live="estado"
                                class="rounded-lg border border-slate-300 px-3 py-2 text-sm bg-white shadow-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition text-black w-[180px]">
                                <option value="" style="color:#222">Todos</option>
                                <option value="requisitado" style="color:#222">Requisitados</option>
                                <option value="pendente" style="color:#222">Pendentes</option>
                                <option value="aceite" style="color:#222">Aceites</option>
                                <option value="recusado" style="color:#222">Recusados</option>
                            </select>
                        @endif
                    </div>
                </div>
                <div class="flex flex-col">
                    <label class="block text-xs font-semibold mb-1 uppercase tracking-wider text-slate-700">Requisitado
                        em</label>
                    <div class="flex gap-2">
                        <input type="date" wire:model.live="dataRequisicaoInicio"
                            class="rounded-lg border border-slate-300 px-2 py-2 text-sm bg-white shadow-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition w-[130px] text-black"
                            placeholder="De" />
                        <span class="text-xs text-slate-400 self-center">a</span>
                        <input type="date" wire:model.live="dataRequisicaoFim"
                            class="rounded-lg border border-slate-300 px-2 py-2 text-sm bg-white shadow-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition w-[130px] text-black"
                            placeholder="Até" />
                    </div>
                </div>
                <div class="flex flex-col">
                    <label class="block text-xs font-semibold mb-1 uppercase tracking-wider text-slate-700">Devolução
                        prevista</label>
                    <div class="flex gap-2">
                        <input type="date" wire:model.live="dataPrevistaInicio"
                            class="rounded-lg border border-slate-300 px-2 py-2 text-sm bg-white shadow-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition w-[130px] text-black"
                            placeholder="De" />
                        <span class="text-xs text-slate-400 self-center">a</span>
                        <input type="date" wire:model.live="dataPrevistaFim"
                            class="rounded-lg border border-slate-300 px-2 py-2 text-sm bg-white shadow-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition w-[130px] text-black"
                            placeholder="Até" />
                    </div>
                </div>
                <div class="flex flex-col">
                    <label class="block text-xs font-semibold mb-1 uppercase tracking-wider text-slate-700">Devolvido
                        em</label>
                    <div class="flex gap-2">
                        <input type="date" wire:model.live="dataDevolucaoInicio"
                            class="rounded-lg border border-slate-300 px-2 py-2 text-sm bg-white shadow-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition w-[130px] text-black"
                            placeholder="De" />
                        <span class="text-xs text-slate-400 self-center">a</span>
                        <input type="date" wire:model.live="dataDevolucaoFim"
                            class="rounded-lg border border-slate-300 px-2 py-2 text-sm bg-white shadow-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition w-[130px] text-black"
                            placeholder="Até" />
                    </div>
                </div>
            </div>
            <div class="mt-4">
                @include('livewire.components.search-bar', [
                    'placeholder' => 'Pesquisar por ISBN, Livro ou Editora...',
                    'model' => 'search',
                ])
            </div>
        </div>
        <div class="overflow-x-auto rounded-xl bg-white shadow mb-8">
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500">
                            <th class="px-4 py-3 bg-slate-50 text-slate-500">Nº</th>
                            <th class="px-4 py-3 bg-slate-50 text-slate-500">Livro</th>
                            <th class="px-4 py-3 bg-slate-50 text-slate-500">Editora</th>
                            <th class="px-4 py-3 bg-slate-50 text-slate-500">Requisitado em</th>
                            <th class="px-4 py-3 bg-slate-50 text-slate-500">Devolução prevista</th>
                            <th class="px-4 py-3 bg-slate-50 text-slate-500">Devolvido em</th>
                            <th class="px-4 py-3 bg-slate-50 text-slate-500 text-right"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse ($minhasRequisicoes as $requisicao)
                            <tr class="align-middle">
                                <td class="px-4 py-3 whitespace-nowrap text-xs align-middle font-bold">
                                    {{ $requisicao->numero ?? '-' }}</td>
                                <td class="px-4 py-3 min-w-56 align-middle">
                                    <div class="flex gap-3 items-center">
                                        <div class="avatar">
                                            <div
                                                class="h-12 w-8 overflow-hidden rounded shadow-sm ring-1 ring-black/5">
                                                @if ($requisicao->livro->imagem_capa)
                                                    <img src="{{ str_starts_with($requisicao->livro->imagem_capa, 'http') ? $requisicao->livro->imagem_capa : asset('storage/' . $requisicao->livro->imagem_capa) }}"
                                                        alt="Capa" class="h-full w-full object-cover" />
                                                @else
                                                    <div
                                                        class="flex h-full w-full items-center justify-center bg-gray-200 text-xs">
                                                        -</div>
                                                @endif
                                            </div>
                                        </div>
                                        <div>
                                            <div class="font-semibold text-slate-900 text-sm">
                                                {{ $requisicao->livro->nome }}</div>
                                            <div class="text-xs text-slate-500 mt-1">ISBN:
                                                {{ $requisicao->livro->isbn }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm align-middle">
                                    {{ $requisicao->livro->editora?->nome ?? '-' }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-xs align-middle">
                                    {{ $requisicao->requisitado_em ? \Carbon\Carbon::parse($requisicao->requisitado_em)->format('d/m/Y') : '-' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-xs align-middle">
                                    {{ $requisicao->fim_previsto_em ? \Carbon\Carbon::parse($requisicao->fim_previsto_em)->format('d/m/Y') : '-' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-xs align-middle">
                                    @if ($requisicao->devolvido_em)
                                        {{ \Carbon\Carbon::parse($requisicao->devolvido_em)->format('d/m/Y') }}
                                    @else
                                        <span
                                            class="inline-block px-2 py-0.5 rounded bg-slate-100 text-slate-800 text-[11px] font-semibold align-middle">Em
                                            posse</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-xs text-right align-middle">
                                    @php $estado = $requisicao->estado_devolucao ?? ''; @endphp
                                    @if (empty($requisicao->pedido_devolucao_em) && $estado === '' && empty($requisicao->devolvido_em))
                                        <button type="button" wire:click="pedirDevolucao({{ $requisicao->id }})"
                                            class="inline-flex items-center rounded-md border border-blue-200 px-3 py-1.5 text-xs font-medium text-blue-700 transition hover:bg-blue-50">
                                            Devolver
                                        </button>
                                    @elseif ($estado === 'pendente')
                                        <span
                                            class="inline-block px-2 py-0.5 rounded bg-yellow-100 text-yellow-800 text-[11px] font-semibold align-middle">Pendente</span>
                                    @elseif ($estado === 'aceite')
                                        <span
                                            class="inline-block px-2 py-0.5 rounded bg-green-100 text-green-800 text-[11px] font-semibold align-middle">Aceite</span>
                                    @elseif ($estado === 'recusado')
                                        <span
                                            class="inline-block px-2 py-0.5 rounded bg-red-100 text-red-800 text-[11px] font-semibold align-middle">Recusado</span>
                                    @else
                                        <span
                                            class="inline-block px-2 py-0.5 rounded bg-blue-100 text-blue-800 text-[11px] font-semibold align-middle">Requisitado</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-xs text-slate-500">Nenhuma
                                    requisição encontrada.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if ($isAdmin)
        <div class="pt-6 text-left text-sm font-semibold uppercase tracking-wider text-slate-500">Todas as requisições
        </div>
        @if (isset($totalAdminAtivas, $totalAdminUltimos30Dias, $totalAdminEntreguesHoje))
            <div class="flex flex-wrap gap-4 mb-6 mt-2">
                <div
                    class="flex-1 min-w-[180px] bg-white rounded-lg shadow border border-slate-200 p-4 flex flex-col items-center">
                    <span
                        class="block text-[13px] text-blue-900/60 font-semibold uppercase tracking-wider text-center mb-1">Requisições
                        Ativas</span>
                    <span class="text-2xl font-bold text-slate-600 mt-1">{{ $totalAdminAtivas }}</span>
                </div>
                <div
                    class="flex-1 min-w-[180px] bg-white rounded-lg shadow border border-slate-200 p-4 flex flex-col items-center">
                    <span
                        class="block text-[13px] text-blue-900/60 font-semibold uppercase tracking-wider text-center mb-1">Últimos
                        30 dias</span>
                    <span class="text-2xl font-bold text-slate-600 mt-1">{{ $totalAdminUltimos30Dias }}</span>
                </div>
                <div
                    class="flex-1 min-w-[180px] bg-white rounded-lg shadow border border-slate-200 p-4 flex flex-col items-center">
                    <span
                        class="block text-[13px] text-blue-900/60 font-semibold uppercase tracking-wider text-center mb-1">Entregues
                        Hoje</span>
                    <span class="text-2xl font-bold text-slate-600 mt-1">{{ $totalAdminEntreguesHoje }}</span>
                </div>
            </div>
        @endif

        {{-- Filtros --}}
        <div class="mb-2">
            <div class="flex flex-wrap gap-6 items-end pt-4 bg-slate-50 rounded-lg px-4 py-3 border border-slate-200">
                <div>
                    <label
                        class="block text-xs font-semibold mb-1 uppercase tracking-wider text-slate-700">Estado</label>
                    <div class="flex gap-2">
                        <select wire:model.live="adminEstado"
                            class="rounded-lg border border-slate-300 px-3 py-2 text-sm bg-white shadow-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition text-black w-[180px]">
                            <option value="" style="color:#222">Todos</option>
                            <option value="requisitado" style="color:#222">Requisitados</option>
                            <option value="aceite" style="color:#222">Aceites</option>
                            <option value="recusado" style="color:#222">Recusados</option>
                        </select>
                    </div>
                </div>
                <div class="flex flex-col">
                    <label class="block text-xs font-semibold mb-1 uppercase tracking-wider text-slate-700">Requisitado
                        em</label>
                    <div class="flex gap-2">
                        <input type="date" wire:model.live="adminDataRequisicaoInicio"
                            class="rounded-lg border border-slate-300 px-2 py-2 text-sm bg-white shadow-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition w-[130px] text-black"
                            placeholder="De" />
                        <span class="text-xs text-slate-400 self-center">a</span>
                        <input type="date" wire:model.live="adminDataRequisicaoFim"
                            class="rounded-lg border border-slate-300 px-2 py-2 text-sm bg-white shadow-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition w-[130px] text-black"
                            placeholder="Até" />
                    </div>
                </div>
                <div class="flex flex-col">
                    <label class="block text-xs font-semibold mb-1 uppercase tracking-wider text-slate-700">Devolução
                        prevista</label>
                    <div class="flex gap-2">
                        <input type="date" wire:model.live="adminDataPrevistaInicio"
                            class="rounded-lg border border-slate-300 px-2 py-2 text-sm bg-white shadow-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition w-[130px] text-black"
                            placeholder="De" />
                        <span class="text-xs text-slate-400 self-center">a</span>
                        <input type="date" wire:model.live="adminDataPrevistaFim"
                            class="rounded-lg border border-slate-300 px-2 py-2 text-sm bg-white shadow-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition w-[130px] text-black"
                            placeholder="Até" />
                    </div>
                </div>
                <div class="flex flex-col">
                    <label class="block text-xs font-semibold mb-1 uppercase tracking-wider text-slate-700">Devolvido
                        em</label>
                    <div class="flex gap-2">
                        <input type="date" wire:model.live="adminDataDevolucaoInicio"
                            class="rounded-lg border border-slate-300 px-2 py-2 text-sm bg-white shadow-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition w-[130px] text-black"
                            placeholder="De" />
                        <span class="text-xs text-slate-400 self-center">a</span>
                        <input type="date" wire:model.live="adminDataDevolucaoFim"
                            class="rounded-lg border border-slate-300 px-2 py-2 text-sm bg-white shadow-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition w-[130px] text-black"
                            placeholder="Até" />
                    </div>
                </div>
            </div>
            <div class="mt-4">
                @if ($isAdmin)
                    @include('livewire.components.search-bar', [
                        'placeholder' => 'Pesquisar por ISBN, Livro, Editora ou Utilizador...',
                        'model' => 'adminSearch',
                    ])
                @else
                    @include('livewire.components.search-bar', [
                        'placeholder' => 'Pesquisar por ISBN, Livro ou Editora...',
                        'model' => 'search',
                    ])
                @endif
            </div>
        </div>
    @endif
    {{-- Tabela - Todas as requisições (Admin) --}}
    @if ($isAdmin)
        <div class="overflow-x-auto rounded-xl bg-white shadow mb-20">
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500">
                            <th class="px-4 py-3 bg-slate-50 text-slate-500">Nº</th>
                            <th class="px-4 py-3 bg-slate-50 text-slate-500">Livro</th>
                            <th class="px-4 py-3 bg-slate-50 text-slate-500">Editora</th>
                            <th class="px-4 py-3 bg-slate-50 text-slate-500">Utilizador</th>
                            <th class="px-4 py-3 bg-slate-50 text-slate-500">Requisitado em</th>
                            <th class="px-4 py-3 bg-slate-50 text-slate-500">Devolução prevista</th>
                            <th class="px-4 py-3 bg-slate-50 text-slate-500">Devolvido em</th>
                            <th class="px-4 py-3 bg-slate-50 text-slate-500 text-right"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse ($historico as $requisicao)
                            <tr class="align-middle">
                                <td class="px-4 py-3 whitespace-nowrap text-xs align-middle font-bold">
                                    {{ $requisicao->numero ?? '-' }}</td>
                                <td class="px-4 py-3 min-w-56 align-middle">
                                    <div class="flex gap-3 items-center">
                                        <div class="avatar">
                                            <div
                                                class="h-12 w-8 overflow-hidden rounded shadow-sm ring-1 ring-black/5">
                                                @if ($requisicao->livro->imagem_capa)
                                                    <img src="{{ str_starts_with($requisicao->livro->imagem_capa, 'http') ? $requisicao->livro->imagem_capa : asset('storage/' . $requisicao->livro->imagem_capa) }}"
                                                        alt="Capa" class="h-full w-full object-cover" />
                                                @else
                                                    <div
                                                        class="flex h-full w-full items-center justify-center bg-gray-200 text-xs">
                                                        -</div>
                                                @endif
                                            </div>
                                        </div>
                                        <div>
                                            <div class="font-semibold text-slate-900 text-sm">
                                                {{ $requisicao->livro->nome }}</div>
                                            <div class="text-xs text-slate-500 mt-1">ISBN:
                                                {{ $requisicao->livro->isbn }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm align-middle">
                                    {{ $requisicao->livro->editora?->nome ?? '-' }}</td>
                                <td class="px-4 py-3 whitespace-nowrap align-middle min-w-[180px]">
                                    @if ($requisicao->user)
                                        <div class="flex items-center gap-3">
                                            <img src="{{ $requisicao->user->profile_photo_url }}" alt="Foto"
                                                class="h-9 w-9 rounded-full object-cover border border-slate-200 shadow-sm bg-white" />
                                            <div class="flex flex-col">
                                                <span
                                                    class="font-semibold text-slate-900 text-xs">{{ $requisicao->user->name }}</span>
                                                <span
                                                    class="text-xs text-slate-500">{{ $requisicao->user->email }}</span>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-xs align-middle">
                                    {{ $requisicao->requisitado_em ? \Carbon\Carbon::parse($requisicao->requisitado_em)->format('d/m/Y') : '-' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-xs align-middle">
                                    {{ $requisicao->fim_previsto_em ? \Carbon\Carbon::parse($requisicao->fim_previsto_em)->format('d/m/Y') : '-' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-xs align-middle">
                                    @if ($requisicao->devolvido_em)
                                        {{ \Carbon\Carbon::parse($requisicao->devolvido_em)->format('d/m/Y') }}
                                    @else
                                        <span
                                            class="inline-block px-2 py-0.5 rounded bg-slate-100 text-slate-800 text-[11px] font-semibold align-middle">Em
                                            posse</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-xs text-right align-middle">
                                    @php
                                        $isOwnRequest = $requisicao->user_id === auth()->id();
                                        $estado = $requisicao->estado_devolucao ?? '';
                                        $isAdminRequest = $isOwnRequest && $isAdmin;
                                    @endphp
                                    @if ($isAdminRequest)
                                        @if (empty($requisicao->devolvido_em))
                                            <span
                                                class="inline-block px-2 py-0.5 rounded bg-blue-100 text-blue-800 text-[11px] font-semibold align-middle">Requisitado</span>
                                        @else
                                            <span
                                                class="inline-block px-2 py-0.5 rounded bg-green-100 text-green-800 text-[11px] font-semibold align-middle">Aceite</span>
                                        @endif
                                    @elseif ($isOwnRequest && empty($requisicao->pedido_devolucao_em) && $estado === '')
                                        <button type="button" wire:click="pedirDevolucao({{ $requisicao->id }})"
                                            class="inline-flex items-center rounded-md border border-blue-200 px-3 py-1.5 text-xs font-medium text-blue-700 transition hover:bg-blue-50">
                                            Devolver
                                        </button>
                                    @elseif (!$isOwnRequest && $estado === 'pendente')
                                        <button type="button" wire:click="aceitarRequisicao({{ $requisicao->id }})"
                                            class="inline-flex items-center rounded-md border border-green-200 px-3 py-1.5 text-xs font-medium text-green-700 transition hover:bg-green-50 mr-1">
                                            Aceitar
                                        </button>
                                        <button type="button" wire:click="recusarRequisicao({{ $requisicao->id }})"
                                            class="inline-flex items-center rounded-md border border-red-200 px-3 py-1.5 text-xs font-medium text-red-700 transition hover:bg-red-50">
                                            Recusar
                                        </button>
                                    @else
                                        @if ($estado === 'pendente')
                                            <span
                                                class="inline-block px-2 py-0.5 rounded bg-yellow-100 text-yellow-800 text-[11px] font-semibold align-middle">Pendente</span>
                                        @elseif ($estado === 'aceite')
                                            <span
                                                class="inline-block px-2 py-0.5 rounded bg-green-100 text-green-800 text-[11px] font-semibold align-middle">Aceite</span>
                                        @elseif ($estado === 'recusado')
                                            <span
                                                class="inline-block px-2 py-0.5 rounded bg-red-100 text-red-800 text-[11px] font-semibold align-middle">Recusado</span>
                                        @else
                                            <span
                                                class="inline-block px-2 py-0.5 rounded bg-blue-100 text-blue-800 text-[11px] font-semibold align-middle">Requisitado</span>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-xs text-slate-500">Nenhum
                                    histórico de requisições encontrado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

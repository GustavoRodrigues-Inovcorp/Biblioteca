<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900">
                    Encomendas
                </h1>
                <p class="text-sm text-slate-600">
                    Listagem de encomendas com estado de pagamento.
                </p>
            </div>
        </div>
    </x-slot>

    <div class="mt-6 grid gap-3 sm:grid-cols-3">
        <div class="rounded-lg border px-4 py-3 border-slate-200 bg-white">
            <p class="text-xs uppercase tracking-wide text-slate-500">Todas</p>
            <p class="mt-1 text-xl font-semibold text-slate-900">{{ $totais['todas'] }}</p>
        </div>
        <div class="rounded-lg border px-4 py-3 border-slate-200 bg-white">
            <p class="text-xs uppercase tracking-wide text-slate-500">Pagas</p>
            <p class="mt-1 text-xl font-semibold text-slate-900">{{ $totais['paga'] }}</p>
        </div>
        <div class="rounded-lg border px-4 py-3 border-slate-200 bg-white">
            <p class="text-xs uppercase tracking-wide text-slate-500">Pendentes</p>
            <p class="mt-1 text-xl font-semibold text-slate-900">{{ $totais['pendente'] }}</p>
        </div>
    </div>

    <div class="mt-6 mb-4">
        <form id="encomendas-filtros-form" method="GET" action="{{ route('admin.encomendas.index') }}" class="space-y-4">
            <div class="flex flex-wrap gap-6 items-end pt-4 bg-slate-50 rounded-lg px-4 py-3 border border-slate-200">
                <div>
                    <label class="block text-xs font-semibold mb-1 uppercase tracking-wider text-slate-700">Estado</label>
                    <select name="status" data-auto-submit="change"
                        class="rounded-lg border border-slate-300 px-3 py-2 text-sm bg-white shadow-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition text-black w-[180px]">
                        <option value="todas" {{ $statusFilter === 'todas' ? 'selected' : '' }}>Todos</option>
                        <option value="paga" {{ $statusFilter === 'paga' ? 'selected' : '' }}>Pagas</option>
                        <option value="pendente" {{ $statusFilter === 'pendente' ? 'selected' : '' }}>Pendentes</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold mb-1 uppercase tracking-wider text-slate-700">Método</label>
                    <select name="metodo" data-auto-submit="change"
                        class="rounded-lg border border-slate-300 px-3 py-2 text-sm bg-white shadow-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition text-black w-[180px]">
                        <option value="" {{ $metodoFilter === '' ? 'selected' : '' }}>Todos</option>
                        <option value="card" {{ $metodoFilter === 'card' ? 'selected' : '' }}>Cartão</option>
                        <option value="mb_way" {{ $metodoFilter === 'mb_way' ? 'selected' : '' }}>MB Way</option>
                        <option value="multibanco" {{ $metodoFilter === 'multibanco' ? 'selected' : '' }}>Multibanco</option>
                    </select>
                </div>

                <div class="flex flex-col">
                    <label class="block text-xs font-semibold mb-1 uppercase tracking-wider text-slate-700">Criada em</label>
                    <div class="flex gap-2">
                        <input type="date" name="data_criacao_inicio" value="{{ $dataCriacaoInicio }}" data-auto-submit="change"
                            class="rounded-lg border border-slate-300 px-2 py-2 text-sm bg-white shadow-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition w-[130px] text-black"
                            placeholder="De" />
                        <span class="text-xs text-slate-400 self-center">a</span>
                        <input type="date" name="data_criacao_fim" value="{{ $dataCriacaoFim }}" data-auto-submit="change"
                            class="rounded-lg border border-slate-300 px-2 py-2 text-sm bg-white shadow-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition w-[130px] text-black"
                            placeholder="Até" />
                    </div>
                </div>

                <div class="flex flex-col">
                    <label class="block text-xs font-semibold mb-1 uppercase tracking-wider text-slate-700">Pago em</label>
                    <div class="flex gap-2">
                        <input type="date" name="data_pagamento_inicio" value="{{ $dataPagamentoInicio }}" data-auto-submit="change"
                            class="rounded-lg border border-slate-300 px-2 py-2 text-sm bg-white shadow-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition w-[130px] text-black"
                            placeholder="De" />
                        <span class="text-xs text-slate-400 self-center">a</span>
                        <input type="date" name="data_pagamento_fim" value="{{ $dataPagamentoFim }}" data-auto-submit="change"
                            class="rounded-lg border border-slate-300 px-2 py-2 text-sm bg-white shadow-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition w-[130px] text-black"
                            placeholder="Até" />
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <div class="relative w-full">
                    <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 3.473 9.766l3.63 3.63a.75.75 0 1 0 1.06-1.06l-3.63-3.63A5.5 5.5 0 0 0 9 3.5ZM5 9a4 4 0 1 1 8 0 4 4 0 0 1-8 0Z" clip-rule="evenodd" />
                    </svg>
                    <input type="text" name="search" value="{{ $search }}" data-auto-submit="search"
                        placeholder="Pesquisar por n.º da encomenda, utilizador, email, método ou estado..."
                        class="w-full rounded-xl border py-2 pl-10 pr-3 text-sm border-slate-300 focus:border-blue-400 focus:ring-2 focus:ring-blue-200" />
                </div>
            </div>
        </form>
    </div>

    <div class="mt-6 overflow-hidden rounded-lg border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Encomenda</th>
                        <th class="px-4 py-3">Cliente</th>
                        <th class="px-4 py-3">Método</th>
                        <th class="px-4 py-3">Estado</th>
                        <th class="px-4 py-3">Total</th>
                        <th class="px-4 py-3">Data</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($encomendas as $encomenda)
                        <tr>
                            <td class="px-4 py-3 font-semibold text-slate-900">{{ $encomenda->numero }}</td>
                            <td class="px-4 py-3 text-slate-700">
                                <p class="font-medium">{{ $encomenda->user?->name ?? 'Utilizador removido' }}</p>
                                <p class="text-xs text-slate-500">{{ $encomenda->user?->email }}</p>
                            </td>
                                <td class="px-4 py-3 text-slate-700">
                                @php
                                    $paymentMethodLabel = match ($encomenda->payment_method) {
                                        'card', 'stripe' => 'Cartão',
                                        'mb_way', 'mbway' => 'MB Way',
                                        'multibanco' => 'Multibanco',
                                        default => strtoupper(str_replace('_', ' ', (string) $encomenda->payment_method)),
                                    };
                                @endphp
                                {{ $paymentMethodLabel }}
                            </td>
                            <td class="px-4 py-3">
                                @if ($encomenda->payment_status === 'paga')
                                    <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700">Paga</span>
                                @else
                                    <span class="inline-flex rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700">Pendente</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-semibold text-slate-900">{{ number_format((float) $encomenda->total, 2, ',', '.') }} €</td>
                            <td class="px-4 py-3 text-slate-600">{{ optional($encomenda->created_at)->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-slate-500">Ainda não existem encomendas registadas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-200 px-4 py-3">
            {{ $encomendas->links() }}
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('encomendas-filtros-form');

            if (!form) {
                return;
            }

            const submitForm = () => form.requestSubmit();

            form.querySelectorAll('[data-auto-submit="change"]').forEach((element) => {
                element.addEventListener('change', submitForm);
            });

            const searchInput = form.querySelector('[data-auto-submit="search"]');

            if (!searchInput) {
                return;
            }

            let timer = null;

            searchInput.addEventListener('input', () => {
                if (timer) {
                    clearTimeout(timer);
                }

                timer = setTimeout(submitForm, 450);
            });
        });
    </script>
</x-admin-layout>

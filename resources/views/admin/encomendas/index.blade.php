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
        <a href="{{ route('admin.encomendas.index', ['status' => 'todas']) }}" class="rounded-lg border px-4 py-3 {{ $statusFilter === 'todas' ? 'border-blue-300 bg-blue-50' : 'border-slate-200 bg-white' }}">
            <p class="text-xs uppercase tracking-wide text-slate-500">Todas</p>
            <p class="mt-1 text-xl font-semibold text-slate-900">{{ $totais['todas'] }}</p>
        </a>
        <a href="{{ route('admin.encomendas.index', ['status' => 'paga']) }}" class="rounded-lg border px-4 py-3 {{ $statusFilter === 'paga' ? 'border-emerald-300 bg-emerald-50' : 'border-slate-200 bg-white' }}">
            <p class="text-xs uppercase tracking-wide text-slate-500">Pagas</p>
            <p class="mt-1 text-xl font-semibold text-emerald-700">{{ $totais['paga'] }}</p>
        </a>
        <a href="{{ route('admin.encomendas.index', ['status' => 'pendente']) }}" class="rounded-lg border px-4 py-3 {{ $statusFilter === 'pendente' ? 'border-amber-300 bg-amber-50' : 'border-slate-200 bg-white' }}">
            <p class="text-xs uppercase tracking-wide text-slate-500">Pendentes</p>
            <p class="mt-1 text-xl font-semibold text-amber-700">{{ $totais['pendente'] }}</p>
        </a>
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
                            <td class="px-4 py-3 text-slate-700">{{ strtoupper(str_replace('_', ' ', $encomenda->payment_method)) }}</td>
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
</x-admin-layout>

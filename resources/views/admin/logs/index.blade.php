<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900">
                    Logs
                </h1>
                <p class="text-sm text-slate-600">
                    Rastreio de ações da aplicação.
                </p>
            </div>
        </div>
    </x-slot>

    <div class="mt-6 mb-4">
        <form id="logs-filtros-form" method="GET" action="{{ route('admin.logs.index') }}" class="space-y-4">
            <div class="flex flex-wrap gap-6 items-end pt-4 bg-slate-50 rounded-lg px-4 py-3 border border-slate-200">
                <div>
                    <label class="block text-xs font-semibold mb-1 uppercase tracking-wider text-slate-700">Módulo</label>
                    <select name="modulo" data-auto-submit="change"
                        class="rounded-lg border border-slate-300 px-3 py-2 text-sm bg-white shadow-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition text-black w-[130px]">
                        <option value="" {{ $moduloFilter === '' ? 'selected' : '' }}>Todos</option>
                        @foreach ($modulos as $modulo)
                            @php
                                $moduloLabel = [
                                    'Requisicao' => 'Requisição',
                                    'Livro' => 'Livro',
                                    'Review' => 'Review',
                                    'User' => 'Utilizador',
                                    'Encomenda' => 'Encomenda',
                                    'Autor' => 'Autor',
                                    'Editora' => 'Editora',
                                ][$modulo] ?? $modulo;
                            @endphp
                            <option value="{{ $modulo }}" {{ $moduloFilter === $modulo ? 'selected' : '' }}>{{ $moduloLabel }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col">
                    <label class="block text-xs font-semibold mb-1 uppercase tracking-wider text-slate-700">Utilizador</label>
                    <input type="text" name="utilizador" value="{{ $utilizadorFilter }}" data-auto-submit="search"
                        placeholder="Nome ou email"
                        class="rounded-lg border border-slate-300 px-3 py-2 text-sm bg-white shadow-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition w-[260px] text-black" />
                </div>

                <div class="flex flex-col">
                    <label class="block text-xs font-semibold mb-1 uppercase tracking-wider text-slate-700">Data</label>
                    <div class="flex gap-2">
                        <input type="date" name="data_inicio" value="{{ $dataInicioFilter }}" data-auto-submit="change"
                            class="rounded-lg border border-slate-300 px-2 py-2 text-sm bg-white shadow-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition w-[130px] text-black"
                            placeholder="De" />
                        <span class="text-xs text-slate-400 self-center">a</span>
                        <input type="date" name="data_fim" value="{{ $dataFimFilter }}" data-auto-submit="change"
                            class="rounded-lg border border-slate-300 px-2 py-2 text-sm bg-white shadow-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition w-[130px] text-black"
                            placeholder="Até" />
                    </div>
                </div>

                <div class="flex flex-col">
                    <label class="block text-xs font-semibold mb-1 uppercase tracking-wider text-slate-700">ID do objeto</label>
                    <input type="text" name="objeto_id" value="{{ $objetoIdFilter }}" data-auto-submit="search"
                        placeholder="Ex.: 123"
                        class="rounded-lg border border-slate-300 px-3 py-2 text-sm bg-white shadow-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition w-[100px] text-black" />
                </div>
            </div>

            <div class="relative w-full">
                <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 3.473 9.766l3.63 3.63a.75.75 0 1 0 1.06-1.06l-3.63-3.63A5.5 5.5 0 0 0 9 3.5ZM5 9a4 4 0 1 1 8 0 4 4 0 0 1-8 0Z" clip-rule="evenodd" />
                </svg>
                <input type="text" name="search" value="{{ $search }}" data-auto-submit="search"
                    placeholder="Pesquisar por alteração, IP ou browser..."
                    class="w-full rounded-xl border border-slate-300 py-2 pl-10 pr-3 text-sm focus:border-blue-400 focus:ring-2 focus:ring-blue-200" />
            </div>

            @if ($searchTooShort)
                <p class="text-xs text-amber-700">A pesquisa global só é aplicada com 3 ou mais caracteres.</p>
            @endif
        </form>
    </div>

    <section class="mt-6 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">
                    <tr>
                        <th class="px-4 py-3 whitespace-nowrap">Data</th>
                        <th class="px-4 py-3 whitespace-nowrap">Hora</th>
                        <th class="px-4 py-3">Utilizador</th>
                        <th class="px-4 py-3 whitespace-nowrap">Módulo</th>
                        <th class="px-4 py-3 whitespace-nowrap">ID do objeto</th>
                        <th class="px-4 py-3">Alteração</th>
                        <th class="px-4 py-3 whitespace-nowrap">IP</th>
                        <th class="px-4 py-3 whitespace-nowrap">Browser</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($logs as $log)
                        <tr class="align-top hover:bg-slate-50/70">
                            <td class="px-4 py-3 text-xs text-slate-700 whitespace-nowrap">{{ optional($log->created_at)->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-xs text-slate-700 whitespace-nowrap">{{ optional($log->created_at)->format('H:i:s') }}</td>
                            <td class="px-4 py-3 text-xs text-slate-700 min-w-[180px]">
                                @if ($log->user?->name)
                                    <div class="font-semibold text-slate-800">{{ $log->user->name }}</div>
                                    <div class="text-[11px] text-slate-500 break-all">{{ $log->user->email }}</div>
                                @else
                                    <span class="inline-flex rounded-full bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-700">Sistema</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-medium text-xs text-slate-800 whitespace-nowrap">{{ $log->modulo_label }}</td>
                            <td class="px-4 py-3 text-xs text-slate-700 whitespace-nowrap">{{ $log->objeto_id ?? '-' }}</td>
                            <td class="px-4 py-3 text-xs text-slate-700 min-w-[340px]">
                                <span class="inline-flex rounded-full bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-700">
                                    {{ $log->acao_label }}
                                </span>

                                @if (! empty($log->alteracoes_formatadas))
                                    <div class="mt-2 space-y-1 text-xs text-slate-600">
                                        @foreach ($log->alteracoes_formatadas as $alteracao)
                                            <div>
                                                <span class="font-semibold">{{ $alteracao['campo'] }}:</span>
                                                <span class="break-all">{{ $alteracao['valor'] }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-700 whitespace-nowrap">{{ $log->ip ?? '-' }}</td>
                            <td class="px-4 py-3 text-xs text-slate-700 whitespace-nowrap" title="{{ $log->browser ?? '' }}">{{ $log->browser_label }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-slate-500">Sem logs registados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-200 bg-slate-50 px-4 py-3">
            {{ $logs->links() }}
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('logs-filtros-form');

            if (!form) {
                return;
            }

            const submitForm = () => form.requestSubmit();

            form.querySelectorAll('[data-auto-submit="change"]').forEach((element) => {
                element.addEventListener('change', submitForm);
            });

            form.querySelectorAll('[data-auto-submit="search"]').forEach((element) => {
                let timer = null;

                element.addEventListener('input', () => {
                    if (timer) {
                        clearTimeout(timer);
                    }

                    timer = setTimeout(submitForm, 1200);
                });
            });
        });
    </script>
</x-admin-layout>

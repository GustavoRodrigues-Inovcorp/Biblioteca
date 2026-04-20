<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900">Dashboard</h1>
                <p class="text-sm text-slate-600">Visão geral da biblioteca, operações e atividade recente.</p>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.12em] text-slate-500">Estado do dia</p>
                <p class="mt-1 text-sm font-medium text-slate-900">{{ now()->translatedFormat('l, d \d\e F \d\e Y') }}</p>
            </div>
        </div>
    </x-slot>

    <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-admin.stat-card label="Livros" :value="$totalLivros" />
        <x-admin.stat-card label="Autores" :value="$totalAutores" />
        <x-admin.stat-card label="Editoras" :value="$totalEditoras" />
        <x-admin.stat-card label="Administradores" :value="$totalAdmins" />
    </section>

    <section class="mt-6 grid gap-6 xl:grid-cols-3">
        <article class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm xl:col-span-2">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-slate-900">Estado operacional</h2>
                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700">Em tempo real</span>
            </div>

            <div class="mt-5 grid gap-4 sm:grid-cols-2">
                <div class="rounded-lg border border-amber-200 bg-amber-50 p-4">
                    <p class="text-xs uppercase tracking-[0.1em] text-amber-700">Requisições ativas</p>
                    <p class="mt-2 text-2xl font-bold text-amber-900">{{ $requisicoesAtivas }}</p>
                    <p class="mt-1 text-xs text-amber-700">Livros atualmente emprestados.</p>
                </div>

                <div class="rounded-lg border border-rose-200 bg-rose-50 p-4">
                    <p class="text-xs uppercase tracking-[0.1em] text-rose-700">Devoluções pendentes</p>
                    <p class="mt-2 text-2xl font-bold text-rose-900">{{ $devolucoesPendentes }}</p>
                    <p class="mt-1 text-xs text-rose-700">Pedidos a aguardar validação.</p>
                </div>

                <div class="rounded-lg border border-blue-200 bg-blue-50 p-4">
                    <p class="text-xs uppercase tracking-[0.1em] text-blue-700">Pagamentos pendentes</p>
                    <p class="mt-2 text-2xl font-bold text-blue-900">{{ $encomendasPendentes }}</p>
                    <p class="mt-1 text-xs text-blue-700">Encomendas por regularizar.</p>
                </div>

                <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs uppercase tracking-[0.1em] text-slate-700">Reviews suspensas</p>
                    <p class="mt-2 text-2xl font-bold text-slate-900">{{ $reviewsSuspensas }}</p>
                    <p class="mt-1 text-xs text-slate-700">Aguardam decisão do administrador.</p>
                </div>
            </div>

            <div class="mt-5 rounded-lg border border-slate-200 bg-slate-50 p-4">
                <div class="flex items-center justify-between gap-3">
                    <p class="text-sm font-semibold text-slate-900">Taxa de encomendas pagas</p>
                    <p class="text-sm font-bold text-slate-900">{{ $taxaPagamentos }}%</p>
                </div>

                <div class="mt-3 h-2 w-full overflow-hidden rounded-full bg-slate-200">
                    <div class="h-full rounded-full bg-emerald-500" style="width: {{ $taxaPagamentos }}%"></div>
                </div>

                <p class="mt-2 text-xs text-slate-600">{{ $encomendasPagas }} de {{ $totalEncomendas }} encomendas concluídas com pagamento validado.</p>
            </div>
        </article>

        <article class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-slate-900">Atividade recente</h2>
                <a href="{{ route('admin.logs.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900">Ver tudo</a>
            </div>

            <div class="mt-4 space-y-3">
                @forelse ($recentLogs as $log)
                    <div class="min-w-0 max-w-full overflow-hidden rounded-lg border border-slate-100 bg-slate-50 p-3">
                        <p class="text-sm font-medium text-slate-900">{{ ucfirst($log->modulo ?? 'Sistema') }}</p>
                        <p class="mt-1 max-w-full break-all text-xs text-slate-600">{{ \Illuminate\Support\Str::limit((string) $log->alteracao, 180) }}</p>
                        <p class="mt-2 text-xs text-slate-500">
                            {{ $log->user?->name ?? 'Sistema' }}
                            <span class="mx-1">•</span>
                            {{ optional($log->created_at)->diffForHumans() ?? 'Sem data' }}
                        </p>
                    </div>
                @empty
                    <div class="rounded-lg border border-dashed border-slate-300 p-4 text-center text-sm text-slate-500">
                        Ainda não existem registos de atividade.
                    </div>
                @endforelse
            </div>
        </article>
    </section>

    <section class="mt-6 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-slate-900">Atalhos rápidos</h2>
        <p class="mt-1 text-sm text-slate-600">Aceda às tarefas administrativas mais frequentes.</p>

        <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            <a href="{{ route('admin.googlebooks.index') }}" class="inline-flex items-center justify-center rounded-md bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-600 focus:ring-offset-2">Importar livro</a>
            <a href="{{ route('admin.livros.export') }}" class="inline-flex items-center justify-center rounded-md bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-600 focus:ring-offset-2">Exportar Excel</a>
            <a href="{{ route('admin.livros.create') }}" class="inline-flex items-center justify-center rounded-md bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-600 focus:ring-offset-2">Inserir livro</a>
            <a href="{{ route('admin.admin-users.create') }}" class="inline-flex items-center justify-center rounded-md bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-600 focus:ring-offset-2">Criar administradores</a>
        </div>
    </section>
</x-admin-layout>

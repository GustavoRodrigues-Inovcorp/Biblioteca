<x-app-layout>
    <section class="bg-gradient-to-r from-blue-900 via-blue-800 to-blue-700 text-white">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8 lg:py-14">
            <div class="mb-6 flex items-center gap-2 text-sm">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-1 text-white/90">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" class="h-4 w-4 shrink-0" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12 11.204 3.046a1.125 1.125 0 0 1 1.592 0L21.75 12M4.5 9.75V19.5A1.5 1.5 0 0 0 6 21h4.5v-5.25a1.5 1.5 0 0 1 1.5-1.5h0a1.5 1.5 0 0 1 1.5 1.5V21H18a1.5 1.5 0 0 0 1.5-1.5V9.75" />
                    </svg>
                    <span class="ml-2 text-xs text-white/90 underline underline-offset-2">Home</span>
                </a>
                <span class="text-xs text-white/80">/</span>
                <span class="text-xs text-white/70">Requisições</span>
            </div>

            <div class="max-w-3xl space-y-4">
                <p class="inline-flex w-fit items-center rounded-full border border-white/40 bg-white/10 px-3 py-1 text-xs font-semibold uppercase tracking-wider text-blue-100">
                    Área pessoal
                </p>
                <h2 class="text-4xl font-semibold leading-tight tracking-tight sm:text-5xl">
                    Acompanha as tuas requisições.
                </h2>
                <p class="max-w-2xl text-base leading-8 text-blue-100 sm:text-lg">
                    Consulta o histórico, estado de entrega e devolução dos livros requisitados de forma rápida.
                </p>
            </div>
        </div>
    </section>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif
            <livewire:requisicao-table />
        </div>
    </div>
</x-app-layout>

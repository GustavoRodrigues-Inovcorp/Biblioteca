<x-layouts.checkout :back-url="route('carrinho.index')" brand="INOVBOOKS">
    <div class="py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-8 flex items-center gap-2 text-sm">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-1 text-slate-800">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" class="h-4 w-4 shrink-0" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12 11.204 3.046a1.125 1.125 0 0 1 1.592 0L21.75 12M4.5 9.75V19.5A1.5 1.5 0 0 0 6 21h4.5v-5.25a1.5 1.5 0 0 1 1.5-1.5h0a1.5 1.5 0 0 1 1.5 1.5V21H18a1.5 1.5 0 0 0 1.5-1.5V9.75" />
                    </svg>
                    <span class="ml-2 text-xs underline underline-offset-2">Home</span>
                </a>
                <span class="text-xs text-slate-400">/</span>
                <a href="{{ route('carrinho.index') }}" class="text-xs text-slate-500 underline underline-offset-2">Carrinho de Compras</a>
                <span class="text-xs text-slate-400">/</span>
                <span class="text-xs text-slate-500">Morada de Entrega</span>
            </div>

            {{-- Etapas --}}
            <div class="mb-6 flex overflow-x-auto justify-center">
                <div class="flex min-w-max items-start gap-3">
                    <div class="flex min-w-[92px] flex-col items-center gap-2">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-blue-800 text-sm font-semibold text-white">1</span>
                        <span class="text-xs font-medium text-blue-800">Morada</span>
                    </div>
                    <span class="mt-4 h-px w-10 bg-slate-300"></span>
                    <div class="flex min-w-[92px] flex-col items-center gap-2">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-slate-200 text-sm font-semibold text-slate-600">2</span>
                        <span class="text-xs font-medium text-slate-500">Pagamento</span>
                    </div>
                    <span class="mt-4 h-px w-10 bg-slate-300"></span>
                    <div class="flex min-w-[92px] flex-col items-center gap-2">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-slate-200 text-sm font-semibold text-slate-600">3</span>
                        <span class="text-xs font-medium text-slate-500">Revisão</span>
                    </div>
                </div>
            </div>

            <div class="grid gap-8 xl:grid-cols-[minmax(0,1fr)_28rem]">
                <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                    <h1 class="text-2xl font-bold text-slate-900">Morada de Entrega</h1>
                    <p class="mt-2 text-sm text-slate-600">Preenche os dados da morada para continuar a tua encomenda.</p>

                    <form method="POST" action="{{ route('checkout.morada-entrega.store') }}" class="mt-8 grid gap-5 sm:grid-cols-2">
                        @csrf

                        @php
                            $selectedMoradaOption = old('morada_option');

                            if ($selectedMoradaOption === null) {
                                $selectedMoradaOption = count($moradasGuardadas ?? []) > 0 ? 'saved:0' : 'new';
                            }
                        @endphp

                        @if (!empty($moradasGuardadas))
                            <div class="sm:col-span-2 space-y-3">
                                <p class="text-sm font-medium text-slate-800">Escolhe uma morada guardada</p>

                                @foreach ($moradasGuardadas as $index => $moradaGuardada)
                                    <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-slate-200 bg-slate-50 p-3 hover:border-blue-300 hover:bg-blue-50">
                                        <input
                                            type="radio"
                                            name="morada_option"
                                            value="saved:{{ $index }}"
                                            class="mt-1 h-4 w-4 border-slate-300 text-blue-700 focus:ring-blue-600"
                                            @checked($selectedMoradaOption === 'saved:' . $index)
                                        >
                                        <span class="text-sm text-slate-700">
                                            <span class="block font-semibold text-slate-900">{{ $moradaGuardada['nome'] }}</span>
                                            <span class="block">{{ $moradaGuardada['morada'] }}</span>
                                            <span class="block">{{ $moradaGuardada['codigo_postal'] }} {{ $moradaGuardada['localidade'] }}</span>
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        @endif

                        <div class="sm:col-span-2">
                            <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-slate-200 p-3 hover:border-blue-300 hover:bg-blue-50">
                                <input
                                    type="radio"
                                    name="morada_option"
                                    value="new"
                                    class="mt-1 h-4 w-4 border-slate-300 text-blue-700 focus:ring-blue-600"
                                    @checked($selectedMoradaOption === 'new')
                                >
                                <span class="text-sm font-medium text-slate-800">Adicionar nova morada</span>
                            </label>
                            @error('morada_option')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2 js-nova-morada-field">
                            <label for="nome" class="mb-1.5 block text-sm font-medium text-slate-700">Nome completo <span class="text-red-600">*</span></label>
                            <input id="nome" name="nome" type="text" value="{{ $moradaEntrega['nome'] ?? '' }}" class="w-full rounded-md border border-slate-300 px-3 py-2.5 text-sm text-slate-800 focus:border-blue-800 focus:outline-none focus:ring-1 focus:ring-blue-800" placeholder="Nome e apelido" />
                        </div>

                        <div class="sm:col-span-2 js-nova-morada-field">
                            <label for="morada" class="mb-1.5 block text-sm font-medium text-slate-700">Morada <span class="text-red-600">*</span></label>
                            <input id="morada" name="morada" type="text" value="{{ $moradaEntrega['morada'] ?? '' }}" class="w-full rounded-md border border-slate-300 px-3 py-2.5 text-sm text-slate-800 focus:border-blue-800 focus:outline-none focus:ring-1 focus:ring-blue-800" placeholder="Rua, n.º, andar" />
                        </div>

                        <div class="js-nova-morada-field">
                            <label for="codigo_postal" class="mb-1.5 block text-sm font-medium text-slate-700">Codigo postal <span class="text-red-600">*</span></label>
                            <input id="codigo_postal" name="codigo_postal" type="text" value="{{ $moradaEntrega['codigo_postal'] ?? '' }}" class="w-full rounded-md border border-slate-300 px-3 py-2.5 text-sm text-slate-800 focus:border-blue-800 focus:outline-none focus:ring-1 focus:ring-blue-800" placeholder="0000-000" />
                        </div>

                        <div class="js-nova-morada-field">
                            <label for="localidade" class="mb-1.5 block text-sm font-medium text-slate-700">Localidade <span class="text-red-600">*</span></label>
                            <input id="localidade" name="localidade" type="text" value="{{ $moradaEntrega['localidade'] ?? '' }}" class="w-full rounded-md border border-slate-300 px-3 py-2.5 text-sm text-slate-800 focus:border-blue-800 focus:outline-none focus:ring-1 focus:ring-blue-800" placeholder="Cidade" />
                        </div>

                        <div class="sm:col-span-2 mt-2 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                            <x-button>
                                Continuar
                            </x-button>
                        </div>
                    </form>

                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const radios = document.querySelectorAll('input[name="morada_option"]');
                            const novaMoradaFields = document.querySelectorAll('.js-nova-morada-field');

                            const updateNovaMoradaVisibility = function () {
                                const selected = document.querySelector('input[name="morada_option"]:checked');
                                const showNewAddressFields = !selected || selected.value === 'new';

                                novaMoradaFields.forEach(function (field) {
                                    field.classList.toggle('hidden', !showNewAddressFields);
                                });
                            };

                            radios.forEach(function (radio) {
                                radio.addEventListener('change', updateNovaMoradaVisibility);
                            });

                            updateNovaMoradaVisibility();
                        });
                    </script>
                </section>

                @include('cidadao.checkout.partials.resumo')
            </div>
        </div>
    </div>
</x-layouts.checkout>

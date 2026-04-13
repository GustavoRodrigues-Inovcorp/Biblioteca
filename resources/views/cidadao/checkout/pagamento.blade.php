<x-layouts.checkout :back-url="route('checkout.morada-entrega')" brand="INOVBOOKS">
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
                <a href="{{ route('checkout.morada-entrega') }}" class="text-xs text-slate-500 underline underline-offset-2">Morada de Entrega</a>
                <span class="text-xs text-slate-400">/</span>
                <span class="text-xs text-slate-500">Pagamento</span>
            </div>

            {{-- Etapas --}}
            <div class="mb-6 flex overflow-x-auto justify-center">
                <div class="flex min-w-max items-start gap-3">
                    <a href="{{ route('checkout.morada-entrega') }}">
                        <div class="flex min-w-[92px] flex-col items-center gap-2">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-slate-200 text-sm font-semibold text-slate-600">1</span>
                            <span class="text-xs font-medium text-slate-500">Morada</span>
                        </div>
                    </a>
                    <span class="mt-4 h-px w-10 bg-slate-300"></span>
                    <div class="flex min-w-[92px] flex-col items-center gap-2">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-blue-800 text-sm font-semibold text-white">2</span>
                        <span class="text-xs font-medium text-blue-800">Pagamento</span>
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
                    <h1 class="text-2xl font-bold text-slate-900">Pagamento</h1>
                    <p class="mt-2 text-sm text-slate-600">Escolha o método de pagamento para continuar a tua encomenda.</p>

                    @if (session('error'))
                        <div class="mt-6 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mt-6 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form id="checkout-payment-form" method="POST" action="{{ route('checkout.pagamento.store') }}" class="mt-6" x-data="{ selectedMethod: '{{ old('payment_method', $paymentMethods[0]['code']) }}', cardSelection: '{{ old('card_selection', !empty($savedCard) ? 'saved' : 'new') }}' }">
                        @csrf
                        <input type="hidden" name="payment_method_id" id="payment-method-id" value="{{ old('payment_method_id') }}">

                        <div class="mt-8 space-y-3">
                            <h2 class="text-lg font-semibold text-slate-900">Escolhe o método de pagamento</h2>

                            @foreach ($paymentMethods as $method)
                                <div
                                    class="rounded-md border border-slate-300 bg-slate-50 p-4 transition hover:border-blue-400 hover:bg-blue-50/40"
                                    :class="selectedMethod === '{{ $method['code'] }}' ? 'border-blue-400 bg-blue-50/60' : ''"
                                >
                                    <label class="block cursor-pointer">
                                        <span class="flex items-start gap-3">
                                            <input
                                                type="radio"
                                                name="payment_method"
                                                value="{{ $method['code'] }}"
                                                @checked(old('payment_method', $paymentMethods[0]['code']) === $method['code'])
                                                x-model="selectedMethod"
                                                class="mt-1 h-4 w-4 border-slate-300 text-blue-700 focus:ring-blue-600"
                                                required
                                            >

                                            <span class="flex w-full items-start justify-between gap-4">
                                                <span>
                                                    <span class="block text-sm font-semibold text-slate-900">{{ $method['label'] }}</span>
                                                    <span class="mt-1 block text-sm text-slate-600">{{ $method['description'] }}</span>
                                                </span>

                                                <span class="shrink-0 self-center">
                                                    @if ($method['code'] === 'card')
                                                        <span class="inline-flex h-12 items-center gap-2">
                                                            <img src="{{ asset('images/payments/visa.png') }}" alt="Visa" class="h-4 w-auto object-contain">
                                                            <img src="{{ asset('images/payments/mastercard.png') }}" alt="Mastercard" class="h-4 w-auto object-contain">
                                                        </span>
                                                    @elseif ($method['code'] === 'mb_way')
                                                        <span class="inline-flex h-12 items-center">
                                                            <img src="{{ asset('images/payments/mbWay.png') }}" alt="MB WAY" class="h-7 w-auto object-contain">
                                                        </span>
                                                    @elseif ($method['code'] === 'multibanco')
                                                        <span class="inline-flex h-12 items-center">
                                                            <img src="{{ asset('images/payments/multibanco.png') }}" alt="Multibanco" class="h-6 w-auto object-contain">
                                                        </span>
                                                    @endif
                                                </span>
                                            </span>
                                        </span>
                                    </label>

                                    @if ($method['code'] === 'card')
                                        <div x-show="selectedMethod === 'card'" class="ml-7 mt-4 rounded-md border border-blue-200 bg-white p-4">
                                            @if (!empty($savedCard))
                                                <div class="mb-3 rounded-md border border-slate-200 bg-slate-50 p-3">
                                                    <label class="flex cursor-pointer items-start gap-3">
                                                        <input
                                                            type="radio"
                                                            name="card_selection"
                                                            value="saved"
                                                            x-model="cardSelection"
                                                            class="mt-1 h-4 w-4 border-slate-300 text-blue-700 focus:ring-blue-600"
                                                        >
                                                        <span>
                                                            <span class="block text-sm font-semibold text-slate-900">Usar cartão guardado</span>
                                                            <span class="block text-sm text-slate-700">
                                                                {{ strtoupper($savedCard['brand']) }} terminado em {{ $savedCard['last4'] }}
                                                                @if (($savedCard['exp_month'] ?? 0) > 0 && ($savedCard['exp_year'] ?? 0) > 0)
                                                                    ({{ str_pad((string) $savedCard['exp_month'], 2, '0', STR_PAD_LEFT) }}/{{ $savedCard['exp_year'] }})
                                                                @endif
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                            @endif

                                            <div class="rounded-md border border-slate-200 bg-slate-50 p-3">
                                                <label class="flex cursor-pointer items-start gap-3">
                                                    <input
                                                        type="radio"
                                                        name="card_selection"
                                                        value="new"
                                                        x-model="cardSelection"
                                                        class="mt-1 h-4 w-4 border-slate-300 text-blue-700 focus:ring-blue-600"
                                                    >
                                                    <span class="block text-sm font-semibold text-slate-900">@if (!empty($savedCard)) Alterar cartão @else Usar novo cartão @endif</span>
                                                </label>
                                            </div>

                                            <div class="mt-4" x-show="cardSelection === 'new'">
                                                <h3 class="text-sm font-semibold text-slate-900">Dados do Cartão</h3>
                                                <div class="mt-4">
                                                    <label for="card-holder-name" class="mb-1 block text-xs font-medium text-slate-700">Nome no cartão</label>
                                                    <input id="card-holder-name" type="text" autocomplete="cc-name" placeholder="Introduz o nome do titular do cartão" class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-800 focus:border-blue-800 focus:outline-none focus:ring-1 focus:ring-blue-800">
                                                </div>

                                                <div class="mt-3">
                                                    <label class="mb-1 block text-xs font-medium text-slate-700">Número do cartão</label>
                                                    <div id="card-number-element" class="w-full rounded-md border border-slate-300 bg-white px-3 py-3"></div>
                                                </div>

                                                <div class="mt-3 grid gap-3 sm:grid-cols-2">
                                                    <div>
                                                        <label class="mb-1 block text-xs font-medium text-slate-700">Validade</label>
                                                        <div id="card-expiry-element" class="w-full rounded-md border border-slate-300 bg-white px-3 py-3"></div>
                                                    </div>
                                                    <div>
                                                        <label class="mb-1 block text-xs font-medium text-slate-700">CVV</label>
                                                        <div id="card-cvc-element" class="w-full rounded-md border border-slate-300 bg-white px-3 py-3"></div>
                                                    </div>
                                                </div>

                                                <label class="mt-3 inline-flex items-start gap-2 text-sm text-slate-700">
                                                    <input type="checkbox" name="save_card" value="1" class="mt-0.5 h-4 w-4 rounded border-slate-300 text-blue-700 focus:ring-blue-600" @checked(old('save_card'))>
                                                    <span>Guardar este cartão para próximas encomendas</span>
                                                </label>
                                            </div>

                                            <p id="card-errors" class="mt-3 hidden rounded-md border border-red-200 bg-red-50 px-3 py-2 text-xs text-red-700"></p>

                                            <p class="mt-3 text-xs text-slate-500">O pagamento só será efetuado após a revisão da encomenda no passo seguinte.</p>
                                        </div>
                                    @endif

                                    @if ($method['code'] === 'mb_way')
                                        <div x-show="selectedMethod === 'mb_way'" class="ml-7 mt-4 rounded-md border border-blue-200 bg-white p-4 text-sm text-slate-700">
                                            <p>Após a revisão da encomenda, poderás finalizar o processo de pagamento através da App MB WAY.</p>
                                            <div class="mt-4">
                                                <label for="mbway_phone" class="mb-1 block text-xs font-medium text-slate-700">Número MB WAY</label>
                                                <input
                                                    id="mbway_phone"
                                                    name="mbway_phone"
                                                    type="text"
                                                    value="{{ $mbwayPhone ?? '' }}"
                                                    inputmode="numeric"
                                                    pattern="[0-9]{9}"
                                                    maxlength="9"
                                                    oninput="this.value = this.value.replace(/\D/g, '').slice(0, 9)"
                                                    autocomplete="tel-national"
                                                    placeholder="9XXXXXXXX"
                                                    class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-800 focus:border-blue-800 focus:outline-none focus:ring-1 focus:ring-blue-800"
                                                >
                                            </div>
                                        </div>
                                    @endif

                                    @if ($method['code'] === 'multibanco')
                                        <div x-show="selectedMethod === 'multibanco'" class="ml-7 mt-4 rounded-md border border-blue-200 bg-white p-4 text-sm text-slate-700">
                                            <p>Dispões de <strong>7 dias</strong> para efetuares o pagamento. Após este prazo, a encomenda será cancelada.</p>
                                            <p class="mt-2">Ao finalizares a encomenda, irá aparecer os dados de pagamento.</p>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                            <x-button id="checkout-payment-submit">
                                Continuar
                            </x-button>
                        </div>
                    </form>
                </section>

                @include('cidadao.checkout.partials.resumo')
            </div>
        </div>
    </div>

    @if (!empty($stripePublishableKey) && collect($paymentMethods)->contains(fn ($method) => $method['code'] === 'card'))
        <script src="https://js.stripe.com/v3/"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const form = document.getElementById('checkout-payment-form');
                const submitButton = document.getElementById('checkout-payment-submit');
                const paymentMethodInput = document.getElementById('payment-method-id');
                const cardErrors = document.getElementById('card-errors');
                const cardHolderName = document.getElementById('card-holder-name');
                const cardNumberContainer = document.getElementById('card-number-element');
                const cardExpiryContainer = document.getElementById('card-expiry-element');
                const cardCvcContainer = document.getElementById('card-cvc-element');

                if (!form || !cardNumberContainer || !cardExpiryContainer || !cardCvcContainer) {
                    return;
                }

                const stripe = Stripe(@json($stripePublishableKey));
                const elements = stripe.elements({ locale: 'pt' });

                const cardNumber = elements.create('cardNumber', {
                    placeholder: '',
                    showIcon: true,
                    disableLink: true,
                });
                const cardExpiry = elements.create('cardExpiry', {
                    placeholder: '',
                });
                const cardCvc = elements.create('cardCvc', {
                    placeholder: '',
                });

                cardNumber.mount('#card-number-element');
                cardExpiry.mount('#card-expiry-element');
                cardCvc.mount('#card-cvc-element');

                const showError = (message) => {
                    cardErrors.textContent = message;
                    cardErrors.classList.remove('hidden');
                };

                const clearError = () => {
                    cardErrors.textContent = '';
                    cardErrors.classList.add('hidden');
                };

                form.addEventListener('submit', async function (event) {
                    const selected = form.querySelector('input[name="payment_method"]:checked');

                    if (!selected || selected.value !== 'card') {
                        paymentMethodInput.value = '';
                        return;
                    }

                    const selectedCardOption = form.querySelector('input[name="card_selection"]:checked')?.value || 'new';

                    if (selectedCardOption === 'saved') {
                        paymentMethodInput.value = '';
                        return;
                    }

                    event.preventDefault();
                    clearError();
                    submitButton.disabled = true;

                    const holderName = (cardHolderName?.value || '').trim();

                    if (!holderName) {
                        showError('Preenche o nome no cartão para continuar.');
                        if (cardHolderName) {
                            cardHolderName.focus();
                        }
                        submitButton.disabled = false;
                        return;
                    }

                    const { paymentMethod, error } = await stripe.createPaymentMethod({
                        type: 'card',
                        card: cardNumber,
                        billing_details: {
                            name: holderName,
                            email: @json($customerEmail),
                        },
                    });

                    if (error) {
                        showError(error.message || 'Não foi possível validar os dados do cartão.');
                        submitButton.disabled = false;
                        return;
                    }

                    paymentMethodInput.value = paymentMethod.id;
                    form.submit();
                });
            });
        </script>
    @endif
</x-layouts.checkout>

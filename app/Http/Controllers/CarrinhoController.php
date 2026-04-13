<?php

namespace App\Http\Controllers;

use App\Models\Encomenda;
use App\Models\Livro;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

class CarrinhoController extends Controller
{
    private function syncCarrinhoTracking(Request $request, ?array $carrinho = null): void
    {
        $user = $request->user();

        if (!$user) {
            return;
        }

        $snapshot = $carrinho ?? $request->session()->get('carrinho', []);

        if (empty($snapshot)) {
            $user->update([
                'cart_items_snapshot' => null,
                'cart_updated_at' => null,
                'cart_abandoned_notified_at' => null,
            ]);

            return;
        }

        $user->update([
            'cart_items_snapshot' => $snapshot,
            'cart_updated_at' => now(),
            'cart_abandoned_notified_at' => null,
        ]);
    }

    private function getOrCreateStripeCustomerId(User $user, StripeClient $stripe): string
    {
        $customerId = trim((string) ($user->stripe_customer_id ?? ''));

        if ($customerId !== '') {
            return $customerId;
        }

        $customer = $stripe->customers->create([
            'email' => $user->email,
            'name' => $user->name,
            'metadata' => [
                'user_id' => (string) $user->id,
            ],
        ]);

        $customerId = (string) $customer->id;

        $user->update([
            'stripe_customer_id' => $customerId,
        ]);

        return $customerId;
    }

    private function getSavedCard(Request $request): ?array
    {
        $user = $request->user();

        if (!$user) {
            return null;
        }

        $savedCard = $user->saved_card;

        if (!is_array($savedCard)) {
            return null;
        }

        $paymentMethodId = trim((string) ($savedCard['payment_method_id'] ?? ''));

        if ($paymentMethodId === '' || !preg_match('/^pm_[A-Za-z0-9]+$/', $paymentMethodId)) {
            return null;
        }

        return [
            'payment_method_id' => $paymentMethodId,
            'brand' => trim((string) ($savedCard['brand'] ?? 'cartão')),
            'last4' => trim((string) ($savedCard['last4'] ?? '')),
            'exp_month' => (int) ($savedCard['exp_month'] ?? 0),
            'exp_year' => (int) ($savedCard['exp_year'] ?? 0),
        ];
    }

    private function persistSavedCardForUser(User $user, StripeClient $stripe, string $paymentMethodId): void
    {
        $customerId = $this->getOrCreateStripeCustomerId($user, $stripe);

        $paymentMethod = $stripe->paymentMethods->retrieve($paymentMethodId, []);
        $attachedCustomerId = trim((string) ($paymentMethod->customer ?? ''));

        if ($attachedCustomerId === '') {
            $stripe->paymentMethods->attach($paymentMethodId, [
                'customer' => $customerId,
            ]);

            $paymentMethod = $stripe->paymentMethods->retrieve($paymentMethodId, []);
        } elseif ($attachedCustomerId !== $customerId) {
            throw new \RuntimeException('O cartão está associado a outro cliente Stripe.');
        }

        $user->update([
            'stripe_customer_id' => $customerId,
            'saved_card' => [
                'payment_method_id' => (string) $paymentMethod->id,
                'brand' => (string) ($paymentMethod->card->brand ?? 'cartão'),
                'last4' => (string) ($paymentMethod->card->last4 ?? ''),
                'exp_month' => (int) ($paymentMethod->card->exp_month ?? 0),
                'exp_year' => (int) ($paymentMethod->card->exp_year ?? 0),
            ],
        ]);
    }

    private function normalizeMoradaData(array $dados): array
    {
        return [
            'nome' => trim((string) ($dados['nome'] ?? '')),
            'morada' => trim((string) ($dados['morada'] ?? '')),
            'codigo_postal' => trim((string) ($dados['codigo_postal'] ?? '')),
            'localidade' => trim((string) ($dados['localidade'] ?? '')),
        ];
    }

    private function isMoradaCompleta(array $morada): bool
    {
        return $morada['nome'] !== ''
            && $morada['morada'] !== ''
            && $morada['codigo_postal'] !== ''
            && $morada['localidade'] !== '';
    }

    private function getSavedMoradasEntrega(Request $request): array
    {
        $user = $request->user();

        if (!$user) {
            return [];
        }

        $moradas = collect($user->delivery_addresses ?? [])
            ->filter(fn ($morada) => is_array($morada))
            ->map(fn (array $morada) => $this->normalizeMoradaData($morada))
            ->filter(fn (array $morada) => $this->isMoradaCompleta($morada))
            ->values();

        $legacyMorada = $this->normalizeMoradaData([
            'nome' => (string) ($user->delivery_nome ?? ''),
            'morada' => (string) ($user->delivery_morada ?? ''),
            'codigo_postal' => (string) ($user->delivery_codigo_postal ?? ''),
            'localidade' => (string) ($user->delivery_localidade ?? ''),
        ]);

        if ($this->isMoradaCompleta($legacyMorada) && !$moradas->contains($legacyMorada)) {
            $moradas->prepend($legacyMorada);
        }

        return $moradas->values()->all();
    }

    private function persistMoradaUtilizador(Request $request, array $morada): void
    {
        $user = $request->user();

        if (!$user) {
            return;
        }

        $morada = $this->normalizeMoradaData($morada);
        $moradas = collect($this->getSavedMoradasEntrega($request));

        if (!$moradas->contains($morada)) {
            $moradas->prepend($morada);
        }

        $user->update([
            'delivery_nome' => $morada['nome'],
            'delivery_morada' => $morada['morada'],
            'delivery_codigo_postal' => $morada['codigo_postal'],
            'delivery_localidade' => $morada['localidade'],
            'delivery_addresses' => $moradas->take(10)->values()->all(),
        ]);
    }

    private function getCartTotalAmountCents(array $resumo): int
    {
        return max(1, (int) round(((float) $resumo['totalPreco']) * 100));
    }

    private function getEncomendaItensSnapshot(array $resumo): array
    {
        return collect($resumo['itens'] ?? [])
            ->map(function (array $item): array {
                $livro = $item['livro'];

                return [
                    'livro_id' => (int) $livro->id,
                    'livro_nome' => (string) $livro->nome,
                    'quantidade' => (int) $item['quantidade'],
                    'preco_unitario' => (float) $livro->preco,
                    'subtotal' => (float) $item['subtotal'],
                ];
            })
            ->values()
            ->all();
    }

    private function createEncomendaRecord(Request $request, array $resumo, string $paymentMethod, string $paymentStatus, array $extra = []): Encomenda
    {
        $nextNumber = ((int) Encomenda::query()->max('numero')) + 1;

        return Encomenda::query()->create([
            'numero' => $nextNumber,
            'user_id' => $request->user()->id,
            'total' => (float) $resumo['totalPreco'],
            'total_itens' => (int) $resumo['totalItens'],
            'payment_method' => $paymentMethod,
            'payment_status' => $paymentStatus,
            'stripe_payment_intent_id' => $extra['stripe_payment_intent_id'] ?? null,
            'stripe_checkout_session_id' => $extra['stripe_checkout_session_id'] ?? null,
            'paid_at' => $extra['paid_at'] ?? null,
            'itens' => $this->getEncomendaItensSnapshot($resumo),
            'morada_entrega' => $request->session()->get('checkout.morada_entrega', []),
            'mbway_phone' => (string) $request->session()->get('checkout.mbway_phone', ''),
        ]);
    }

    private function markEncomendaAsPaidByCheckoutSession(string $checkoutSessionId, ?string $paymentIntentId = null): void
    {
        $query = Encomenda::query()
            ->where('stripe_checkout_session_id', $checkoutSessionId);

        $encomenda = $query->latest()->first();

        if (!$encomenda) {
            return;
        }

        $encomenda->update([
            'payment_status' => 'paga',
            'paid_at' => $encomenda->paid_at ?? now(),
            'stripe_payment_intent_id' => $paymentIntentId ?: $encomenda->stripe_payment_intent_id,
        ]);
    }

    private function getSelectedPaymentMethod(Request $request): ?array
    {
        $selectedPaymentMethod = (string) $request->session()->get('checkout.payment_method', '');

        if ($selectedPaymentMethod === '') {
            return null;
        }

        return collect($this->getStripePaymentMethods())
            ->firstWhere('code', $selectedPaymentMethod);
    }

    private function getStripePaymentMethods(): array
    {
        $configuredMethods = collect(explode(',', (string) config('services.stripe.checkout_methods', 'card,mb_way,multibanco')))
            ->map(fn (string $method) => trim($method))
            ->filter(fn (string $method) => $method !== '')
            ->values();

        $catalog = [
            'card' => [
                'label' => 'Cartão de Crédito ou Débito',
                'description' => 'Pagamento imediato com cartão bancário.',
            ],
            'mb_way' => [
                'label' => 'MB WAY',
                'description' => 'Pagamento rápido e fácil com a App MB WAY.',
            ],
            'multibanco' => [
                'label' => 'Referência Multibanco',
                'description' => 'Pagamento por entidade e referência Multibanco.',
            ],
        ];

        return $configuredMethods
            ->filter(fn (string $method) => array_key_exists($method, $catalog))
            ->map(fn (string $method) => [
                'code' => $method,
                ...$catalog[$method],
            ])
            ->values()
            ->all();
    }

    private function getMoradaEntregaData(Request $request): array
    {
        $user = $request->user();
        $sessionMorada = $request->session()->get('checkout.morada_entrega', []);

        return $this->normalizeMoradaData([
            'nome' => old('nome', $sessionMorada['nome'] ?? $user?->delivery_nome ?? ''),
            'morada' => old('morada', $sessionMorada['morada'] ?? $user?->delivery_morada ?? ''),
            'codigo_postal' => old('codigo_postal', $sessionMorada['codigo_postal'] ?? $user?->delivery_codigo_postal ?? ''),
            'localidade' => old('localidade', $sessionMorada['localidade'] ?? $user?->delivery_localidade ?? ''),
        ]);
    }

    private function getResumoCarrinhoData(Request $request): array
    {
        $carrinho = $request->session()->get('carrinho', []);
        $livros = Livro::whereIn('id', array_keys($carrinho))->with('autores')->get()->keyBy('id');

        $itens = collect($carrinho)
            ->map(function ($quantidade, $livroId) use ($livros) {
                $livro = $livros->get((int) $livroId);

                if (!$livro) {
                    return null;
                }

                $quantidade = (int) $quantidade;
                $preco = (float) $livro->preco;

                return [
                    'livro' => $livro,
                    'quantidade' => $quantidade,
                    'subtotal' => $preco * $quantidade,
                ];
            })
            ->filter()
            ->values();

        $totalItens = (int) $itens->sum('quantidade');
        $totalPreco = (float) $itens->sum('subtotal');
        $subtotalSemIva = $totalPreco / 1.06;
        $valorIva = $totalPreco - $subtotalSemIva;

        return [
            'itens' => $itens,
            'totalItens' => $totalItens,
            'totalPreco' => $totalPreco,
            'subtotalSemIva' => $subtotalSemIva,
            'valorIva' => $valorIva,
            'envio' => 0.0,
        ];
    }

    private function getMultibancoDetailsFromPaymentIntent(object $paymentIntent, array $resumo): ?array
    {
        $displayDetails = data_get($paymentIntent, 'next_action.display_multibanco_details')
            ?? data_get($paymentIntent, 'next_action.multibanco_display_details');

        if (!is_object($displayDetails) && !is_array($displayDetails)) {
            return null;
        }

        $referencia = (string) (data_get($displayDetails, 'reference') ?? '');
        $entidade = (string) (data_get($displayDetails, 'entity') ?? '');
        $expiraEm = data_get($displayDetails, 'expires_at');
        $voucherUrl = (string) (data_get($displayDetails, 'hosted_voucher_url') ?? '');

        return [
            'referencia' => $referencia,
            'entidade' => $entidade,
            'valor' => (float) ($resumo['totalPreco'] ?? 0),
            'expira_em' => is_numeric($expiraEm) ? (int) $expiraEm : null,
            'voucher_url' => $voucherUrl,
        ];
    }

    public function index(Request $request): View
    {
        return view('cidadao.carrinho.index', $this->getResumoCarrinhoData($request));
    }

    public function add(Request $request, Livro $livro): RedirectResponse
    {
        $carrinho = $request->session()->get('carrinho', []);
        $livroId = (string) $livro->id;

        $carrinho[$livroId] = ((int) ($carrinho[$livroId] ?? 0)) + 1;

        $request->session()->put('carrinho', $carrinho);
        $this->syncCarrinhoTracking($request, $carrinho);

        return back()->with('cart_preview_open', true);
    }

    public function remove(Request $request, Livro $livro): RedirectResponse
    {
        $carrinho = $request->session()->get('carrinho', []);
        unset($carrinho[(string) $livro->id]);

        $request->session()->put('carrinho', $carrinho);
        $this->syncCarrinhoTracking($request, $carrinho);

        if ($request->boolean('keep_preview')) {
            return back()->with('cart_preview_open', true);
        }

        return back();
    }

    public function clear(Request $request): RedirectResponse
    {
        $request->session()->forget('carrinho');
        $this->syncCarrinhoTracking($request, []);

        return back()->with('success', 'Carrinho limpo com sucesso.');
    }

    public function moradaEntrega(Request $request): View|RedirectResponse
    {
        $resumo = $this->getResumoCarrinhoData($request);

        if ($resumo['itens']->isEmpty()) {
            return redirect()->route('carrinho.index')->with('success', 'Adiciona livros ao carrinho antes de finalizar a encomenda.');
        }

        return view('cidadao.checkout.morada-entrega', [
            ...$resumo,
            'moradaEntrega' => $this->getMoradaEntregaData($request),
            'moradasGuardadas' => $this->getSavedMoradasEntrega($request),
        ]);
    }

    public function storeMoradaEntrega(Request $request): RedirectResponse
    {
        $resumo = $this->getResumoCarrinhoData($request);

        if ($resumo['itens']->isEmpty()) {
            return redirect()->route('carrinho.index')->with('success', 'Adiciona livros ao carrinho antes de finalizar a encomenda.');
        }

        $moradasGuardadas = $this->getSavedMoradasEntrega($request);
        $moradaOption = (string) $request->input('morada_option', 'new');

        if (str_starts_with($moradaOption, 'saved:')) {
            $moradaIndex = (int) str_replace('saved:', '', $moradaOption);
            $dados = $moradasGuardadas[$moradaIndex] ?? null;

            if (!$dados) {
                return back()->withErrors([
                    'morada_option' => 'Seleciona uma morada guardada válida ou adiciona uma nova morada.',
                ])->withInput();
            }
        } else {
            $dados = $request->validate([
                'nome' => ['required', 'string', 'max:120'],
                'morada' => ['required', 'string', 'max:255'],
                'codigo_postal' => ['required', 'string', 'max:20'],
                'localidade' => ['required', 'string', 'max:120'],
            ]);
        }

        $dados = $this->normalizeMoradaData($dados);

        $request->session()->put('checkout.morada_entrega', $dados);
        $this->persistMoradaUtilizador($request, $dados);

        return redirect()->route('checkout.pagamento');
    }

    public function pagamento(Request $request): View|RedirectResponse
    {
        $resumo = $this->getResumoCarrinhoData($request);

        if ($resumo['itens']->isEmpty()) {
            return redirect()->route('carrinho.index')->with('success', 'Adiciona livros ao carrinho antes de finalizar a encomenda.');
        }

        $paymentMethods = $this->getStripePaymentMethods();

        if (empty($paymentMethods)) {
            return redirect()->route('checkout.morada-entrega')->with('error', 'Não existem métodos de pagamento configurados.');
        }

        $stripePublishableKey = null;

        if (collect($paymentMethods)->contains(fn (array $method) => $method['code'] === 'card')) {
            $stripePublishableKey = (string) config('services.stripe.key');

            if ($stripePublishableKey === '') {
                return redirect()->route('checkout.morada-entrega')->with('error', 'Stripe não configurado para pagamento com cartão.');
            }
        }

        return view('cidadao.checkout.pagamento', [
            ...$resumo,
            'paymentMethods' => $paymentMethods,
            'stripePublishableKey' => $stripePublishableKey,
            'customerEmail' => (string) ($request->user()?->email ?? ''),
            'savedCard' => $this->getSavedCard($request),
            'mbwayPhone' => old('mbway_phone', (string) $request->session()->get('checkout.mbway_phone', '')),
        ]);
    }

    public function storePagamento(Request $request): RedirectResponse
    {
        $resumo = $this->getResumoCarrinhoData($request);

        if ($resumo['itens']->isEmpty()) {
            return redirect()->route('carrinho.index')->with('success', 'Adiciona livros ao carrinho antes de finalizar a encomenda.');
        }

        $paymentMethods = collect($this->getStripePaymentMethods());

        if ($paymentMethods->isEmpty()) {
            return redirect()->route('checkout.pagamento')->with('error', 'Não existem métodos de pagamento configurados.');
        }

        $allowedMethodCodes = $paymentMethods->pluck('code')->all();

        $dados = $request->validate([
            'payment_method' => ['required', 'string', 'in:' . implode(',', $allowedMethodCodes)],
            'payment_method_id' => ['nullable', 'string', 'regex:/^pm_[A-Za-z0-9]+$/'],
            'card_selection' => ['nullable', 'string', 'in:saved,new'],
            'save_card' => ['nullable', 'boolean'],
            'mbway_phone' => ['nullable', 'string', 'max:20'],
        ]);

        $request->session()->put('checkout.payment_method', $dados['payment_method']);

        if ($dados['payment_method'] === 'card') {
            $savedCard = $this->getSavedCard($request);
            $cardSelection = (string) ($dados['card_selection'] ?? (!empty($savedCard) ? 'saved' : 'new'));

            if ($cardSelection === 'saved') {
                if (empty($savedCard['payment_method_id'])) {
                    return redirect()->route('checkout.pagamento')->with('error', 'Não existe cartão guardado. Escolhe a opção de novo cartão.');
                }

                $request->session()->put('checkout.card_selection', 'saved');
                $request->session()->put('checkout.card_payment_method_id', $savedCard['payment_method_id']);
                $request->session()->forget('checkout.save_card');
            } else {
                if (empty($dados['payment_method_id'])) {
                    return redirect()->route('checkout.pagamento')->with('error', 'Preenche os dados do cartão antes de continuar.');
                }

                $request->session()->put('checkout.card_selection', 'new');
                $request->session()->put('checkout.card_payment_method_id', $dados['payment_method_id']);
                $request->session()->put('checkout.save_card', (bool) ($dados['save_card'] ?? false));
            }
            $request->session()->forget('checkout.mbway_phone');
        } elseif ($dados['payment_method'] === 'mb_way') {
            $mbwayPhone = preg_replace('/\D+/', '', (string) ($dados['mbway_phone'] ?? ''));

            if ($mbwayPhone === '') {
                return redirect()->route('checkout.pagamento')->withErrors([
                    'mbway_phone' => 'Indica o número de telemóvel para pagamento com MB WAY.',
                ])->withInput();
            }

            if (!preg_match('/^\d{9}$/', $mbwayPhone)) {
                return redirect()->route('checkout.pagamento')->withErrors([
                    'mbway_phone' => 'O número MB WAY deve ter exatamente 9 dígitos numéricos.',
                ])->withInput();
            }

            $request->session()->put('checkout.mbway_phone', $mbwayPhone);

            $request->session()->forget('checkout.card_selection');
            $request->session()->forget('checkout.card_payment_method_id');
            $request->session()->forget('checkout.save_card');
        } else {
            $request->session()->forget('checkout.mbway_phone');
            $request->session()->forget('checkout.card_selection');
            $request->session()->forget('checkout.card_payment_method_id');
            $request->session()->forget('checkout.save_card');
        }

        return redirect()->route('checkout.revisao');
    }

    public function revisao(Request $request): View|RedirectResponse
    {
        $resumo = $this->getResumoCarrinhoData($request);

        if ($resumo['itens']->isEmpty()) {
            return redirect()->route('carrinho.index')->with('success', 'Adiciona livros ao carrinho antes de finalizar a encomenda.');
        }

        $morada = $request->session()->get('checkout.morada_entrega', []);

        if (empty($morada['nome']) || empty($morada['morada']) || empty($morada['codigo_postal']) || empty($morada['localidade'])) {
            return redirect()->route('checkout.morada-entrega')->with('error', 'Preenche a morada de entrega antes de continuar para a revisão.');
        }

        $selectedPaymentMethod = $this->getSelectedPaymentMethod($request);

        if (!$selectedPaymentMethod) {
            return redirect()->route('checkout.pagamento')->with('error', 'Escolhe um método de pagamento antes de continuar para a revisão.');
        }

        if (($selectedPaymentMethod['code'] ?? null) === 'card' && !$request->session()->has('checkout.card_payment_method_id')) {
            return redirect()->route('checkout.pagamento')->with('error', 'Preenche os dados do cartão antes de continuar para a revisão.');
        }

        $mbwayPhone = (string) $request->session()->get('checkout.mbway_phone', '');

        if (($selectedPaymentMethod['code'] ?? null) === 'mb_way' && $mbwayPhone === '') {
            return redirect()->route('checkout.pagamento')->with('error', 'Indica o número MB WAY antes de continuar para a revisão.');
        }

        return view('cidadao.checkout.revisao', [
            ...$resumo,
            'moradaEntrega' => $morada,
            'selectedPaymentMethod' => $selectedPaymentMethod,
            'mbwayPhone' => $mbwayPhone,
        ]);
    }

    public function pagarComStripe(Request $request): RedirectResponse
    {
        $resumo = $this->getResumoCarrinhoData($request);

        if ($resumo['itens']->isEmpty()) {
            return redirect()->route('carrinho.index')->with('success', 'Adiciona livros ao carrinho antes de finalizar a encomenda.');
        }

        $paymentMethods = collect($this->getStripePaymentMethods());

        if ($paymentMethods->isEmpty()) {
            return redirect()->route('checkout.pagamento')->with('error', 'Não existem métodos de pagamento configurados.');
        }

        $selectedPaymentMethod = (string) $request->session()->get('checkout.payment_method', '');

        if ($selectedPaymentMethod === '') {
            $allowedMethodCodes = $paymentMethods->pluck('code')->all();

            $dados = $request->validate([
                'payment_method' => ['required', 'string', 'in:' . implode(',', $allowedMethodCodes)],
            ]);

            $selectedPaymentMethod = $dados['payment_method'];
        }

        if (!$paymentMethods->contains(fn (array $method) => $method['code'] === $selectedPaymentMethod)) {
            return redirect()->route('checkout.pagamento')->with('error', 'Método de pagamento inválido.');
        }

        $morada = $request->session()->get('checkout.morada_entrega', []);

        if (empty($morada['nome']) || empty($morada['morada']) || empty($morada['codigo_postal']) || empty($morada['localidade'])) {
            return redirect()->route('checkout.morada-entrega')->with('error', 'Preenche a morada de entrega antes de continuar para o pagamento.');
        }

        $stripeSecret = config('services.stripe.secret');

        if (blank($stripeSecret)) {
            return redirect()->route('checkout.pagamento')->with('error', 'Stripe não configurado.');
        }

        if ($selectedPaymentMethod === 'card') {
            $paymentMethodId = (string) $request->session()->get('checkout.card_payment_method_id', '');
            $cardSelection = (string) $request->session()->get('checkout.card_selection', 'new');

            if ($paymentMethodId === '') {
                return redirect()->route('checkout.pagamento')->with('error', 'Preenche os dados do cartão antes de finalizar a encomenda.');
            }

            try {
                $stripe = new StripeClient($stripeSecret);

                $paymentIntentData = [
                    'amount' => $this->getCartTotalAmountCents($resumo),
                    'currency' => 'eur',
                    'payment_method_types' => ['card'],
                    'payment_method' => $paymentMethodId,
                    'confirm' => true,
                    'receipt_email' => $request->user()?->email,
                    'metadata' => [
                        'user_id' => (string) $request->user()->id,
                        'payment_method' => $selectedPaymentMethod,
                    ],
                ];

                if ($cardSelection === 'saved') {
                    $customerId = trim((string) ($request->user()?->stripe_customer_id ?? ''));

                    if ($customerId !== '') {
                        $paymentIntentData['customer'] = $customerId;
                    }
                }

                $shouldSaveCard = (bool) $request->session()->get('checkout.save_card', false)
                    && $cardSelection === 'new'
                    && $request->user() instanceof User;

                if ($shouldSaveCard) {
                    try {
                        /** @var User $user */
                        $user = $request->user();
                        $customerId = $this->getOrCreateStripeCustomerId($user, $stripe);
                        $paymentMethod = $stripe->paymentMethods->retrieve($paymentMethodId, []);
                        $attachedCustomerId = trim((string) ($paymentMethod->customer ?? ''));

                        if ($attachedCustomerId === '') {
                            $stripe->paymentMethods->attach($paymentMethodId, [
                                'customer' => $customerId,
                            ]);
                        } elseif ($attachedCustomerId !== $customerId) {
                            throw new \RuntimeException('O cartão está associado a outro cliente Stripe.');
                        }

                        $paymentIntentData['customer'] = $customerId;
                    } catch (\Throwable $exception) {
                        Log::warning('Não foi possível preparar o cartão para guardar antes do pagamento.', [
                            'message' => $exception->getMessage(),
                            'user_id' => $request->user()?->id,
                        ]);

                        $request->session()->put('checkout.save_card', false);
                    }
                }

                $paymentIntent = $stripe->paymentIntents->create([
                    ...$paymentIntentData,
                ]);
            } catch (ApiErrorException $exception) {
                Log::error('Erro ao confirmar PaymentIntent Stripe.', [
                    'message' => $exception->getMessage(),
                    'user_id' => $request->user()?->id,
                    'payment_method' => $selectedPaymentMethod,
                ]);

                return redirect()->route('checkout.revisao')->with('error', 'Pagamento recusado. Verifica os dados do cartão e tenta novamente.');
            }

            if (($paymentIntent->status ?? null) !== 'succeeded') {
                return redirect()->route('checkout.revisao')->with('error', 'Pagamento não concluído. Tenta novamente com outro cartão.');
            }

            $this->createEncomendaRecord($request, $resumo, 'card', 'paga', [
                'stripe_payment_intent_id' => (string) ($paymentIntent->id ?? ''),
                'paid_at' => now(),
            ]);

            if ((bool) $request->session()->get('checkout.save_card', false) && $cardSelection === 'new' && $request->user()) {
                try {
                    $this->persistSavedCardForUser($request->user(), $stripe, $paymentMethodId);
                } catch (\Throwable $exception) {
                    Log::warning('Não foi possível guardar o cartão para o utilizador.', [
                        'message' => $exception->getMessage(),
                        'user_id' => $request->user()?->id,
                    ]);
                }
            }

            $request->session()->forget('carrinho');
            $request->session()->forget('checkout.morada_entrega');
            $request->session()->forget('checkout.payment_method');
            $request->session()->forget('checkout.mbway_phone');
            $request->session()->forget('checkout.card_selection');
            $request->session()->forget('checkout.card_payment_method_id');
            $request->session()->forget('checkout.save_card');
            $this->syncCarrinhoTracking($request, []);

            return redirect()->route('carrinho.index')->with('success', 'Encomenda concluída com sucesso.');
        }

        try {
            $stripe = new StripeClient($stripeSecret);

            $paymentIntentData = [
                'amount' => $this->getCartTotalAmountCents($resumo),
                'currency' => 'eur',
                'payment_method_types' => [$selectedPaymentMethod],
                'confirm' => true,
                'receipt_email' => $request->user()?->email,
                'metadata' => [
                    'user_id' => (string) $request->user()->id,
                    'payment_method' => $selectedPaymentMethod,
                    'mbway_phone' => (string) $request->session()->get('checkout.mbway_phone', ''),
                ],
            ];

            if ($selectedPaymentMethod === 'mb_way') {
                $mbwayPhone = preg_replace('/\D+/', '', (string) $request->session()->get('checkout.mbway_phone', ''));

                if (strlen((string) $mbwayPhone) !== 9) {
                    return redirect()->route('checkout.pagamento')->with('error', 'Número MB WAY inválido.');
                }

                $paymentIntentData['payment_method_data'] = [
                    'type' => 'mb_way',
                    'billing_details' => [
                        'phone' => '+351' . $mbwayPhone,
                    ],
                ];
            } elseif ($selectedPaymentMethod === 'multibanco') {
                $billingDetails = array_filter([
                    'name' => (string) ($request->user()?->name ?: $request->session()->get('checkout.morada_entrega.nome', '')),
                    'email' => (string) ($request->user()?->email ?: ''),
                ], fn ($value) => (string) $value !== '');

                $paymentIntentData['payment_method_data'] = [
                    'type' => 'multibanco',
                    'billing_details' => $billingDetails,
                ];
            }

            $paymentIntent = $stripe->paymentIntents->create($paymentIntentData);

            $intentStatus = (string) ($paymentIntent->status ?? '');

            if ($intentStatus === 'succeeded') {
                $this->createEncomendaRecord($request, $resumo, $selectedPaymentMethod, 'paga', [
                    'stripe_payment_intent_id' => (string) ($paymentIntent->id ?? ''),
                    'paid_at' => now(),
                ]);

                $request->session()->forget('carrinho');
                $request->session()->forget('checkout.morada_entrega');
                $request->session()->forget('checkout.payment_method');
                $request->session()->forget('checkout.mbway_phone');
                $request->session()->forget('checkout.card_selection');
                $request->session()->forget('checkout.card_payment_method_id');
                $request->session()->forget('checkout.save_card');
                $this->syncCarrinhoTracking($request, []);

                return redirect()->route('carrinho.index')->with('success', 'Encomenda concluída com sucesso.');
            }

            if (in_array($intentStatus, ['processing', 'requires_action'], true)) {
                $this->createEncomendaRecord($request, $resumo, $selectedPaymentMethod, 'pendente', [
                    'stripe_payment_intent_id' => (string) ($paymentIntent->id ?? ''),
                ]);

                $pendingPaymentMessage = match ($selectedPaymentMethod) {
                    'mb_way' => 'Pedido MB WAY enviado. Confirma o pagamento na app MB WAY.',
                    'multibanco' => 'Referência Multibanco gerada. Consulta os dados de pagamento e liquida dentro do prazo.',
                    default => 'Pedido criado com sucesso. O pagamento encontra-se pendente.',
                };

                $request->session()->forget('carrinho');
                $request->session()->forget('checkout.morada_entrega');
                $request->session()->forget('checkout.payment_method');
                $request->session()->forget('checkout.mbway_phone');
                $request->session()->forget('checkout.card_selection');
                $request->session()->forget('checkout.card_payment_method_id');
                $request->session()->forget('checkout.save_card');
                $this->syncCarrinhoTracking($request, []);

                $redirect = redirect()->route('carrinho.index')->with('success', $pendingPaymentMessage);

                if ($selectedPaymentMethod === 'multibanco') {
                    $multibancoDetails = $this->getMultibancoDetailsFromPaymentIntent($paymentIntent, $resumo);

                    if ($multibancoDetails) {
                        $redirect->with('multibanco_details', $multibancoDetails);
                    }
                }

                return $redirect;
            }

            return redirect()->route('checkout.revisao')->with('error', 'Não foi possível iniciar o pagamento para o método selecionado.');
        } catch (ApiErrorException $exception) {
            Log::error('Erro ao criar sessao Stripe.', [
                'message' => $exception->getMessage(),
                'user_id' => $request->user()?->id,
                'payment_method' => $selectedPaymentMethod,
            ]);

            return redirect()->route('checkout.pagamento')->with('error', 'Não foi possível iniciar o pagamento para o método selecionado.');
        }
    }

    public function stripeSuccess(Request $request): RedirectResponse
    {
        $sessionId = (string) $request->query('session_id', '');
        $stripeSecret = config('services.stripe.secret');

        if ($sessionId === '' || blank($stripeSecret)) {
            return redirect()->route('checkout.pagamento')->with('error', 'Não foi possível validar o pagamento.');
        }

        try {
            $stripe = new StripeClient($stripeSecret);
            $checkoutSession = $stripe->checkout->sessions->retrieve($sessionId, []);

            if (($checkoutSession->payment_status ?? null) !== 'paid') {
                return redirect()->route('checkout.pagamento')->with('error', 'Pagamento ainda não confirmado.');
            }

            $this->markEncomendaAsPaidByCheckoutSession(
                $sessionId,
                (string) ($checkoutSession->payment_intent ?? '')
            );
        } catch (ApiErrorException $exception) {
            Log::error('Erro ao validar sessao Stripe.', [
                'message' => $exception->getMessage(),
                'session_id' => $sessionId,
                'user_id' => $request->user()?->id,
            ]);

            return redirect()->route('checkout.pagamento')->with('error', 'Não foi possível validar o pagamento.');
        }

        $request->session()->forget('carrinho');
        $request->session()->forget('checkout.morada_entrega');
        $request->session()->forget('checkout.payment_method');
        $request->session()->forget('checkout.mbway_phone');
        $request->session()->forget('checkout.card_selection');
        $request->session()->forget('checkout.card_payment_method_id');
        $request->session()->forget('checkout.save_card');
        $this->syncCarrinhoTracking($request, []);

        return redirect()->route('carrinho.index')->with('success', 'Encomenda concluída com sucesso.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Encomenda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

class EncomendaController extends Controller
{
    public function index(Request $request): View
    {
        $statusFilter = (string) $request->query('status', 'todas');
        $metodoFilter = trim((string) $request->query('metodo', ''));
        $dataCriacaoInicio = trim((string) $request->query('data_criacao_inicio', ''));
        $dataCriacaoFim = trim((string) $request->query('data_criacao_fim', ''));
        $dataPagamentoInicio = trim((string) $request->query('data_pagamento_inicio', ''));
        $dataPagamentoFim = trim((string) $request->query('data_pagamento_fim', ''));
        $search = trim((string) $request->query('search', ''));

        $allowedStatuses = ['todas', 'paga', 'pendente'];
        $allowedMetodos = ['card', 'mb_way', 'multibanco'];
        $metodoAliases = [
            'card' => ['card', 'stripe'],
            'mb_way' => ['mb_way', 'mbway'],
            'multibanco' => ['multibanco'],
        ];

        if (!in_array($statusFilter, $allowedStatuses, true)) {
            $statusFilter = 'todas';
        }

        if (!in_array($metodoFilter, $allowedMetodos, true)) {
            $metodoFilter = '';
        }

        $query = Encomenda::query()->with('user')->latest();

        if ($statusFilter !== 'todas') {
            $query->where('payment_status', $statusFilter);
        }

        if ($metodoFilter !== '') {
            $query->whereIn('payment_method', $metodoAliases[$metodoFilter] ?? [$metodoFilter]);
        }

        if ($dataCriacaoInicio !== '') {
            $query->whereDate('created_at', '>=', $dataCriacaoInicio);
        }

        if ($dataCriacaoFim !== '') {
            $query->whereDate('created_at', '<=', $dataCriacaoFim);
        }

        if ($dataPagamentoInicio !== '') {
            $query->whereDate('paid_at', '>=', $dataPagamentoInicio);
        }

        if ($dataPagamentoFim !== '') {
            $query->whereDate('paid_at', '<=', $dataPagamentoFim);
        }

        if ($search !== '') {
            $query->where(function ($subQuery) use ($search): void {
                $subQuery
                    ->where('numero', 'like', "%{$search}%")
                    ->orWhere('payment_method', 'like', "%{$search}%")
                    ->orWhere('payment_status', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search): void {
                        $userQuery
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $encomendas = $query->paginate(20)->withQueryString();

        $this->syncPendingEncomendasWithStripe($encomendas->getCollection());

        return view('admin.encomendas.index', [
            'encomendas' => $encomendas,
            'statusFilter' => $statusFilter,
            'metodoFilter' => $metodoFilter,
            'dataCriacaoInicio' => $dataCriacaoInicio,
            'dataCriacaoFim' => $dataCriacaoFim,
            'dataPagamentoInicio' => $dataPagamentoInicio,
            'dataPagamentoFim' => $dataPagamentoFim,
            'search' => $search,
            'totais' => [
                'todas' => Encomenda::query()->count(),
                'paga' => Encomenda::query()->where('payment_status', 'paga')->count(),
                'pendente' => Encomenda::query()->where('payment_status', 'pendente')->count(),
            ],
        ]);
    }

    private function syncPendingEncomendasWithStripe($encomendas): void
    {
        $stripeSecret = trim((string) config('services.stripe.secret', ''));

        if ($stripeSecret === '') {
            return;
        }

        $pendingEncomendas = $encomendas
            ->where('payment_status', 'pendente')
            ->filter(fn (Encomenda $encomenda): bool => trim((string) $encomenda->stripe_payment_intent_id) !== '');

        if ($pendingEncomendas->isEmpty()) {
            return;
        }

        $stripe = new StripeClient($stripeSecret);

        /** @var Encomenda $encomenda */
        foreach ($pendingEncomendas as $encomenda) {
            try {
                $paymentIntent = $stripe->paymentIntents->retrieve((string) $encomenda->stripe_payment_intent_id, []);
                $status = (string) ($paymentIntent->status ?? '');

                if ($status === 'succeeded') {
                    $encomenda->update([
                        'payment_status' => 'paga',
                        'paid_at' => $encomenda->paid_at ?? now(),
                    ]);
                }
            } catch (ApiErrorException $exception) {
                Log::warning('Falha ao sincronizar encomenda pendente.', [
                    'encomenda_id' => $encomenda->id,
                    'payment_intent_id' => $encomenda->stripe_payment_intent_id,
                    'message' => $exception->getMessage(),
                ]);
            }
        }
    }
}

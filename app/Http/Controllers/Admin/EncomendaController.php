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
        $allowedStatuses = ['todas', 'paga', 'pendente'];

        if (!in_array($statusFilter, $allowedStatuses, true)) {
            $statusFilter = 'todas';
        }

        $query = Encomenda::query()->with('user')->latest();

        if ($statusFilter !== 'todas') {
            $query->where('payment_status', $statusFilter);
        }

        $encomendas = $query->paginate(20)->withQueryString();

        $this->syncPendingEncomendasWithStripe($encomendas->getCollection());

        return view('admin.encomendas.index', [
            'encomendas' => $encomendas,
            'statusFilter' => $statusFilter,
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

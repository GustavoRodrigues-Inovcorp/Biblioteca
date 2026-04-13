<?php

namespace App\Http\Controllers;

use App\Models\Encomenda;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Event;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $payload = (string) $request->getContent();
        $signature = (string) $request->header('Stripe-Signature', '');
        $webhookSecret = trim((string) config('services.stripe.webhook_secret', ''));

        try {
            if ($webhookSecret !== '') {
                $event = Webhook::constructEvent($payload, $signature, $webhookSecret);
            } else {
                $decoded = json_decode($payload, true);

                if (!is_array($decoded)) {
                    return response()->json(['received' => false], 400);
                }

                $event = Event::constructFrom($decoded);
            }
        } catch (SignatureVerificationException $exception) {
            Log::warning('Assinatura de webhook Stripe inválida.', [
                'message' => $exception->getMessage(),
            ]);

            return response()->json(['received' => false], 400);
        } catch (\UnexpectedValueException $exception) {
            Log::warning('Payload inválido no webhook Stripe.', [
                'message' => $exception->getMessage(),
            ]);

            return response()->json(['received' => false], 400);
        }

        $eventType = (string) ($event->type ?? '');

        if (!in_array($eventType, [
            'payment_intent.succeeded',
            'payment_intent.payment_failed',
            'charge.succeeded',
            'charge.failed',
        ], true)) {
            return response()->json(['received' => true]);
        }

        $eventObject = $event->data->object ?? null;
        $paymentIntentId = trim((string) (
            $eventObject->id
            ?? $eventObject->payment_intent
            ?? ''
        ));

        if ($paymentIntentId === '') {
            return response()->json(['received' => true]);
        }

        $encomenda = Encomenda::query()
            ->where('stripe_payment_intent_id', $paymentIntentId)
            ->latest()
            ->first();

        if (!$encomenda) {
            return response()->json(['received' => true]);
        }

        if (in_array($eventType, ['payment_intent.succeeded', 'charge.succeeded'], true)) {
            $encomenda->update([
                'payment_status' => 'paga',
                'paid_at' => $encomenda->paid_at ?? now(),
            ]);
        }

        if (in_array($eventType, ['payment_intent.payment_failed', 'charge.failed'], true)) {
            $encomenda->update([
                'payment_status' => 'pendente',
            ]);
        }

        return response()->json(['received' => true]);
    }
}

<?php

namespace App\Console\Commands;

use App\Mail\CarrinhoAbandonadoMail;
use App\Models\Livro;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendAbandonedCartReminder extends Command
{
    protected $signature = 'carrinho:abandono';

    protected $description = 'Envia email a cidadaos com carrinho abandonado ha mais de 1 hora.';

    public function handle(): int
    {
        $limite = now()->subHour();

        $users = User::query()
            ->where('role', User::ROLE_CIDADAO)
            ->whereNotNull('email')
            ->whereNotNull('cart_items_snapshot')
            ->whereNotNull('cart_updated_at')
            ->where('cart_updated_at', '<=', $limite)
            ->where(function ($query): void {
                $query->whereNull('cart_abandoned_notified_at')
                    ->orWhereColumn('cart_abandoned_notified_at', '<', 'cart_updated_at');
            })
            ->get();

        $emailsEnviados = 0;

        /** @var User $user */
        foreach ($users as $user) {
            $snapshot = $user->cart_items_snapshot;

            if (!is_array($snapshot) || empty($snapshot)) {
                continue;
            }

            $livroIds = collect(array_keys($snapshot))
                ->map(fn ($id) => (int) $id)
                ->filter(fn (int $id): bool => $id > 0)
                ->values();

            if ($livroIds->isEmpty()) {
                continue;
            }

            $livros = Livro::query()
                ->whereIn('id', $livroIds)
                ->get(['id', 'nome'])
                ->keyBy('id');

            $itens = $livroIds
                ->map(function (int $livroId) use ($snapshot, $livros): ?array {
                    $livro = $livros->get($livroId);

                    if (!$livro) {
                        return null;
                    }

                    return [
                        'titulo' => (string) $livro->nome,
                        'quantidade' => max(1, (int) ($snapshot[(string) $livroId] ?? 1)),
                    ];
                })
                ->filter()
                ->values()
                ->all();

            if (empty($itens)) {
                continue;
            }

            Mail::to($user->email)->send(new CarrinhoAbandonadoMail($user, $itens));

            $user->forceFill([
                'cart_abandoned_notified_at' => now(),
            ])->save();

            $emailsEnviados++;
        }

        $this->info('Notificacoes de carrinho abandonado enviadas: ' . $emailsEnviados . '.');

        return self::SUCCESS;
    }
}

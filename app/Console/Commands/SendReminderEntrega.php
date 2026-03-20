<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Requisicao;
use App\Mail\ReminderEntregaMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendReminderEntrega extends Command
{
    protected $signature = 'reminder:entrega';
    protected $description = 'Envia um email de lembrete ao cidadão um dia antes da data de entrega do livro.';

    public function handle()
    {
        $amanha = Carbon::tomorrow()->toDateString();
        $requisicoes = Requisicao::whereDate('fim_previsto_em', $amanha)
            ->whereNull('devolvido_em')
            ->with(['user', 'livro.autores', 'livro.editora'])
            ->get();

        foreach ($requisicoes as $requisicao) {
            if ($requisicao->user && $requisicao->user->email) {
                Mail::to($requisicao->user->email)
                    ->send(new ReminderEntregaMail($requisicao));
            }
        }

        $this->info('Lembretes enviados para ' . $requisicoes->count() . ' requisições.');
    }
}

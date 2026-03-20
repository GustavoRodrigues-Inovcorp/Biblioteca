<?php

namespace App\Mail;

use App\Models\Requisicao;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReminderEntregaMail extends Mailable
{
    use SerializesModels;

    public $requisicao;
    public $livro;
    public $user;

    public function __construct(Requisicao $requisicao)
    {
        $this->requisicao = $requisicao;
        $this->livro = $requisicao->livro;
        $this->user = $requisicao->user;
    }

    public function build()
    {
        return $this->subject('Lembrete: Entrega do Livro Amanhã')
            ->view('emails.reminder-entrega');
    }
}

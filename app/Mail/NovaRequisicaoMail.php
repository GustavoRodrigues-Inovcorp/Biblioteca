<?php

namespace App\Mail;

use App\Models\Requisicao;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels as QueueSerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NovaRequisicaoMail extends Mailable
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
        return $this->subject('Nova Requisição de Livro')
            ->view('emails.nova-requisicao');
    }
}

<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CarrinhoAbandonadoMail extends Mailable
{
    use SerializesModels;

    public User $user;

    /** @var array<int, array{titulo:string, quantidade:int}> */
    public array $itens;

    /**
     * @param array<int, array{titulo:string, quantidade:int}> $itens
     */
    public function __construct(User $user, array $itens)
    {
        $this->user = $user;
        $this->itens = $itens;
    }

    public function build(): self
    {
        return $this->subject('Precisa de ajuda para concluir a sua compra?')
            ->view('emails.carrinho-abandonado');
    }
}

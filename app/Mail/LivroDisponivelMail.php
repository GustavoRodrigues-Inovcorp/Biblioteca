<?php
namespace App\Mail;

use App\Models\Livro;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LivroDisponivelMail extends Mailable
{
    use Queueable, SerializesModels;

    public $livro;

    public function __construct(Livro $livro)
    {
        $this->livro = $livro;
    }

    public function build()
    {
        return $this->subject('O livro "' . $this->livro->nome . '" está disponível!')
            ->markdown('emails.livros.disponivel');
    }
}

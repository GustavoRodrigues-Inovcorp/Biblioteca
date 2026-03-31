<?php

namespace App\Mail;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EstadoReviewMail extends Mailable
{
    use Queueable, SerializesModels;

    public $review;
    public $estado;
    public $justificacao;

    public function __construct(Review $review)
    {
        $this->review = $review;
        $this->estado = $review->estado;
        $this->justificacao = $review->justificacao;
    }

    public function build()
    {
        $subject = $this->estado === 'ativo'
            ? 'A sua review foi aprovada'
            : 'A sua review foi recusada';
        return $this->subject($subject)
            ->markdown('emails.estado-review');
    }
}

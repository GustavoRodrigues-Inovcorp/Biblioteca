<?php

namespace App\Livewire\Admin;

use App\Models\Review;
use Livewire\Component;
use Livewire\WithPagination;

class ReviewsTable extends Component
{
    use WithPagination;

    public $estado = '';

    public $popupReviewId = null;
    public $popupAcao = null; // 'ativo' ou 'recusado'
    public $showPopup = false;
    public $justificacao = '';

    public function confirmarAcao($reviewId, $acao)
    {
        $this->popupReviewId = $reviewId;
        $this->popupAcao = $acao;
        $this->showPopup = true;
        $this->justificacao = '';
    }

    public function executarAcao()
    {
        if ($this->popupReviewId && $this->popupAcao) {
            $review = Review::findOrFail($this->popupReviewId);
            $review->estado = $this->popupAcao;
            if ($this->popupAcao === 'recusado') {
                $review->justificacao = $this->justificacao;
            } else {
                $review->justificacao = null;
            }
            $review->save();

            // Enviar email ao cidadão
            if ($review->user && $review->user->email) {
                \Mail::to($review->user->email)->send(new \App\Mail\EstadoReviewMail($review));
            }

            session()->flash('status', 'Estado do review atualizado!');
        }
        $this->showPopup = false;
        $this->popupReviewId = null;
        $this->popupAcao = null;
        $this->justificacao = '';
    }

    public function cancelarPopup()
    {
        $this->showPopup = false;
        $this->popupReviewId = null;
        $this->popupAcao = null;
        $this->justificacao = '';
    }

    public function render()
    {
        $reviews = Review::with(['user', 'livro'])
            ->when($this->estado, fn($q) => $q->where('estado', $this->estado))
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('livewire.admin.reviews-table', [
            'reviews' => $reviews,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Livro;
use App\Models\Requisicao;
use Illuminate\View\View;

class LivroPublicoController extends Controller
{
    public function show(Livro $livro): View
    {
        $livro->load(['autores', 'editora', 'reviews.user']);
        $user = auth()->user();
        $historicoQuery = Requisicao::with('user')
            ->where('livro_id', $livro->id);

        if ($user && !$user->isAdmin()) {
            $historicoQuery->where('user_id', $user->id);
        }

        $historico = $historicoQuery->orderByDesc('requisitado_em')->get();

        $livrosRequisitados = 0;
        if ($user && !$user->isAdmin()) {
            $livrosRequisitados = $user->requisicoes()->whereNull('devolvido_em')->count();
        }

        // Verificar se o cidadão pode deixar review
        $podeReview = false;
        $meuReview = null;
        if ($user && !$user->isAdmin()) {
            // Já devolveu este livro?
            $devolvida = $user->requisicoes()->where('livro_id', $livro->id)->whereNotNull('devolvido_em')->exists();
            $meuReview = $livro->reviews->where('user_id', $user->id)->first();
            $podeReview = $devolvida && !$meuReview;
        }
        $relacionados = $livro->relacionados();
        return view('livro-detalhe', [
            'livro' => $livro,
            'historico' => $historico,
            'livrosRequisitados' => $livrosRequisitados,
            'podeReview' => $podeReview,
            'meuReview' => $meuReview,
            'relacionados' => $relacionados,
        ]);
    }
}

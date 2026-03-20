<?php

namespace App\Http\Controllers;

use App\Models\Livro;
use App\Models\Requisicao;
use Illuminate\View\View;

class LivroPublicoController extends Controller
{
    public function show(Livro $livro): View
    {
        $livro->load(['autores', 'editora']);
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

        return view('livro-detalhe', [
            'livro' => $livro,
            'historico' => $historico,
            'livrosRequisitados' => $livrosRequisitados,
        ]);
    }
}

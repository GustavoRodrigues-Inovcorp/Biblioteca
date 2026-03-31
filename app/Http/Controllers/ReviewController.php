<?php

namespace App\Http\Controllers;

use App\Models\Requisicao;
use App\Models\Review;
use App\Models\Livro;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, $livro): RedirectResponse
    {
        $user = $request->user();
        $livro = Livro::findOrFail($livro);
        if ($user->role !== 'cidadao') {
            abort(403, 'Apenas cidadãos podem submeter reviews.');
        }
        // Só pode submeter review se já devolveu pelo menos uma requisição deste livro e ainda não tiver review
        $devolvida = $user->requisicoes()->where('livro_id', $livro->id)->whereNotNull('devolvido_em')->exists();
        $jaReview = $livro->reviews()->where('user_id', $user->id)->exists();
        if (!$devolvida || $jaReview) {
            abort(403, 'Não pode submeter review para este livro.');
        }
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comentario' => 'nullable|string|max:1000',
        ]);
        // Procurar a requisição devolvida mais recente
        $requisicao = $user->requisicoes()->where('livro_id', $livro->id)->whereNotNull('devolvido_em')->latest('devolvido_em')->first();
        Review::create([
            'user_id' => $user->id,
            'livro_id' => $livro->id,
            'requisicao_id' => $requisicao?->id,
            'rating' => $validated['rating'],
            'comentario' => $validated['comentario'] ?? null,
        ]);
        return redirect()->route('livros.show', $livro)->with('status', 'Review submetido com sucesso!');
    }
}

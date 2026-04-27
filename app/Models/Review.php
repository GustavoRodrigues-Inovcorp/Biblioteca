<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo Review
 *
 * Representa uma avaliação feita por um utilizador a um livro, normalmente associada a uma requisição.
 * Inclui rating, comentário, estado e justificação.
 */
class Review extends Model
{
    use HasFactory;

    // Campos que podem ser preenchidos em massa
    protected $fillable = [
        'user_id',
        'livro_id',
        'requisicao_id',
        'rating',
        'comentario',
        'estado',
        'justificacao',
    ];

    /**
     * Relação: review pertence a um utilizador.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relação: review pertence a um livro.
     */
    public function livro(): BelongsTo
    {
        return $this->belongsTo(Livro::class);
    }

    /**
     * Relação: review pertence a uma requisição (opcional).
     */
    public function requisicao(): BelongsTo
    {
        return $this->belongsTo(Requisicao::class);
    }
}

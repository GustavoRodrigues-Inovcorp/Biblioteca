<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo Requisicao
 *
 * Representa um pedido de requisição de um livro por um utilizador.
 * Guarda datas de requisição, devolução, estado e relações com utilizador e livro.
 */
class Requisicao extends Model
{
    // Nome da tabela (caso não siga o padrão Laravel)
    protected $table = 'requisicoes';

    // Campos que podem ser preenchidos em massa
    protected $fillable = [
        'numero',
        'user_id',
        'livro_id',
        'requisitado_em',
        'fim_previsto_em',
        'devolvido_em',
        'pedido_devolucao_em',
        'estado_devolucao',
    ];

    // Casts automáticos de tipos para datas
    protected function casts(): array
    {
        return [
            'requisitado_em' => 'datetime',
            'fim_previsto_em' => 'datetime',
            'devolvido_em' => 'datetime',
            'pedido_devolucao_em' => 'datetime',
        ];
    }

    /**
     * Relação: requisição pertence a um utilizador.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relação: requisição pertence a um livro.
     */
    public function livro(): BelongsTo
    {
        return $this->belongsTo(Livro::class);
    }

    /**
     * Verifica se a requisição está ativa (ainda não foi devolvida).
     */
    public function isAtiva(): bool
    {
        return $this->devolvido_em === null;
    }
}

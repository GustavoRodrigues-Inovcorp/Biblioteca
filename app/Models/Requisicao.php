<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Requisicao extends Model
{
    protected $table = 'requisicoes';

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

    protected function casts(): array
    {
        return [
            'requisitado_em' => 'datetime',
            'fim_previsto_em' => 'datetime',
            'devolvido_em' => 'datetime',
            'pedido_devolucao_em' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function livro(): BelongsTo
    {
        return $this->belongsTo(Livro::class);
    }

    public function isAtiva(): bool
    {
        return $this->devolvido_em === null;
    }
}

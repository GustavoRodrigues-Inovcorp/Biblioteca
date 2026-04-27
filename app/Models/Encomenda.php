<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo Encomenda
 *
 * Representa uma encomenda feita por um utilizador, incluindo total, itens, estado de pagamento e morada de entrega.
 */
class Encomenda extends Model
{
    // Nome da tabela (caso não siga o padrão Laravel)
    protected $table = 'encomendas';

    // Campos que podem ser preenchidos em massa
    protected $fillable = [
        'numero',
        'user_id',
        'total',
        'total_itens',
        'payment_method',
        'payment_status',
        'stripe_payment_intent_id',
        'stripe_checkout_session_id',
        'mbway_phone',
        'paid_at',
        'itens',
        'morada_entrega',
    ];

    // Casts automáticos de tipos para alguns campos
    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
            'paid_at' => 'datetime',
            'itens' => 'array',
            'morada_entrega' => 'array',
        ];
    }

    /**
     * Relação: encomenda pertence a um utilizador.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

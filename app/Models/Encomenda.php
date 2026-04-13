<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Encomenda extends Model
{
    protected $table = 'encomendas';

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

    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
            'paid_at' => 'datetime',
            'itens' => 'array',
            'morada_entrega' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

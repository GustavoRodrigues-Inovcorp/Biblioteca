<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'livro_id',
        'requisicao_id',
        'rating',
        'comentario',
        'estado',
        'justificacao',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function livro(): BelongsTo
    {
        return $this->belongsTo(Livro::class);
    }

    public function requisicao(): BelongsTo
    {
        return $this->belongsTo(Requisicao::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Livro extends Model
{
    protected $fillable = [
        'isbn',
        'nome',
        'editora_id',
        'bibliografia',
        'imagem_capa',
        'preco',
    ];

    protected $casts = [
        'preco' => 'decimal:2',
    ];

    /**
     * Relação com Editora (muitos livros para uma editora)
     */
    public function editora(): BelongsTo
    {
        return $this->belongsTo(Editora::class);
    }

    /**
     * Relação com Autores (muitos para muitos)
     * Requer tabela pivot: autor_livro
     */
    public function autores(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Autor::class, 'autor_livro', 'livros_id', 'autores_id');
    }
}

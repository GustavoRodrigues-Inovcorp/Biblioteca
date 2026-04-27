<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Modelo Autor
 *
 * Representa um autor de livros, com nome e foto.
 * Um autor pode estar associado a vários livros (relação muitos para muitos).
 */
class Autor extends Model
{
    // Nome da tabela (caso não siga o padrão Laravel)
    protected $table = 'autores';

    // Campos que podem ser preenchidos em massa
    protected $fillable = [
        'nome',
        'foto',
    ];

    /**
     * Relação: um autor pode ter vários livros (muitos para muitos).
     */
    public function livros(): BelongsToMany
    {
        return $this->belongsToMany(Livro::class, 'autor_livro', 'autores_id', 'livros_id');
    }
}
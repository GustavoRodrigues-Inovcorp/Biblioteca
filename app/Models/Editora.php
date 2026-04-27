<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modelo Editora
 *
 * Representa uma editora de livros, com nome e logotipo.
 * Uma editora pode ter vários livros associados.
 */
class Editora extends Model
{
    // Nome da tabela (caso não siga o padrão Laravel)
    protected $table = 'editoras';

    // Campos que podem ser preenchidos em massa
    protected $fillable = [
        'nome',
        'logotipo',
    ];

    /**
     * Relação: uma editora pode ter vários livros.
     */
    public function livros(): HasMany
    {
        return $this->hasMany(Livro::class, 'editora_id');
    }
}
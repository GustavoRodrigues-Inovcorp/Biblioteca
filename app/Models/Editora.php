<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Editora extends Model
{
    protected $table = 'editoras';

    protected $fillable = [
        'nome',
        'logotipo',
    ];

    public function livros(): HasMany
    {
        return $this->hasMany(Livro::class, 'editora_id');
    }
}
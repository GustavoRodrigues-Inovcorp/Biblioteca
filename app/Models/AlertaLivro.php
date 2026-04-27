<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo AlertaLivro
 *
 * Representa um alerta criado por um utilizador para ser notificado quando um livro ficar disponível.
 */
class AlertaLivro extends Model
{
    use HasFactory;
    // Nome da tabela (caso não siga o padrão Laravel)
    protected $table = 'alertas_livro';
    // Campos que podem ser preenchidos em massa
    protected $fillable = ['user_id', 'livro_id'];

    /**
     * Relação: alerta pertence a um utilizador.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relação: alerta pertence a um livro.
     */
    public function livro()
    {
        return $this->belongsTo(Livro::class);
    }
}

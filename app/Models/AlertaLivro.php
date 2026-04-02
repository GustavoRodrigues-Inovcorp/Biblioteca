<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlertaLivro extends Model
{
    use HasFactory;
    protected $table = 'alertas_livro';
    protected $fillable = ['user_id', 'livro_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function livro()
    {
        return $this->belongsTo(Livro::class);
    }
}

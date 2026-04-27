<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo ActivityLog
 *
 * Regista atividades dos utilizadores na aplicação (logs de ações).
 * Guarda o utilizador, módulo, objeto, alteração, IP, browser e data.
 */
class ActivityLog extends Model
{
    // Nome da tabela (caso não siga o padrão Laravel)
    protected $table = 'logs';

    // Não usa os timestamps automáticos do Laravel
    public $timestamps = false;

    // Campos que podem ser preenchidos em massa
    protected $fillable = [
        'user_id',
        'modulo',
        'objeto_id',
        'alteracao',
        'ip',
        'browser',
        'created_at',
    ];

    // Casts automáticos de tipos para alguns campos
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    /**
     * Relação: log pertence a um utilizador.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

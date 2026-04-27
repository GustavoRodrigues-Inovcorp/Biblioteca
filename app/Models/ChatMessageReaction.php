<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo ChatMessageReaction
 *
 * Representa uma reação (emoji) de um utilizador a uma mensagem de chat.
 */
class ChatMessageReaction extends Model
{
    use HasFactory;

    // Campos que podem ser preenchidos em massa
    protected $fillable = [
        'chat_message_id',
        'user_id',
        'emoji',
    ];

    /**
     * Relação: reação pertence a uma mensagem de chat.
     */
    public function message(): BelongsTo
    {
        return $this->belongsTo(ChatMessage::class, 'chat_message_id');
    }

    /**
     * Relação: reação pertence a um utilizador.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
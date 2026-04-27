<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modelo ChatMessage
 *
 * Representa uma mensagem enviada numa conversa de chat.
 * Pode ter anexos, referência a outra mensagem (resposta) e reações.
 */
class ChatMessage extends Model
{
    use HasFactory;

    // Campos que podem ser preenchidos em massa
    protected $fillable = [
        'conversation_id',
        'user_id',
        'replied_to_message_id',
        'body',
        'attachment_path',
        'attachment_name',
        'attachment_mime',
        'attachment_size',
    ];

    /**
     * Relação: mensagem pertence a uma conversa de chat.
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(ChatConversation::class, 'conversation_id');
    }

    /**
     * Relação: mensagem pertence a um utilizador.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relação: mensagem a que esta está a responder (opcional).
     */
    public function repliedToMessage(): BelongsTo
    {
        return $this->belongsTo(self::class, 'replied_to_message_id');
    }

    /**
     * Relação: mensagem pode ter várias reações (emojis).
     */
    public function reactions(): HasMany
    {
        return $this->hasMany(ChatMessageReaction::class, 'chat_message_id');
    }
}
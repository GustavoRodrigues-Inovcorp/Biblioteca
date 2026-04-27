<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo ChatRoomInvitation
 *
 * Representa um convite para um utilizador entrar numa sala de chat.
 * Liga a conversa, o utilizador convidado e quem convidou.
 */
class ChatRoomInvitation extends Model
{
    // Campos que podem ser preenchidos em massa
    protected $fillable = ['conversation_id', 'invited_by_id', 'user_id', 'status'];

    /**
     * Relação: convite pertence a uma conversa de chat.
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(ChatConversation::class);
    }

    /**
     * Relação: utilizador que fez o convite.
     */
    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by_id');
    }

    /**
     * Relação: utilizador convidado.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

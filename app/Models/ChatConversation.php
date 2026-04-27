<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modelo ChatConversation
 *
 * Representa uma conversa de chat, que pode ser direta (entre utilizadores) ou de sala (grupo).
 * Liga participantes, mensagens e o criador da conversa.
 */
class ChatConversation extends Model
{
    use HasFactory;

    // Tipos de conversa possíveis
    public const TYPE_DIRECT = 'direct';
    public const TYPE_ROOM = 'room';

    // Campos que podem ser preenchidos em massa
    protected $fillable = [
        'type',
        'name',
        'avatar',
        'created_by_id',
        'last_message_at',
    ];

    // Casts automáticos de tipos para alguns campos
    protected function casts(): array
    {
        return [
            'last_message_at' => 'datetime',
        ];
    }

    /**
     * Relação: utilizador que criou a conversa.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    /**
     * Relação: participantes da conversa (muitos para muitos).
     */
    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'chat_conversation_user')
            ->withPivot('last_read_at', 'role')
            ->withTimestamps();
    }

    /**
     * Relação: mensagens desta conversa.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'conversation_id');
    }

    /**
     * Última mensagem da conversa.
     */
    public function latestMessage(): HasOne
    {
        return $this->hasOne(ChatMessage::class, 'conversation_id')->latestOfMany();
    }

    /**
     * Verifica se a conversa é do tipo sala (grupo).
     */
    public function isRoom(): bool
    {
        return $this->type === self::TYPE_ROOM;
    }

    /**
     * Verifica se a conversa é do tipo direta (entre utilizadores).
     */
    public function isDirect(): bool
    {
        return $this->type === self::TYPE_DIRECT;
    }
}
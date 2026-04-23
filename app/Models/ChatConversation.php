<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatConversation extends Model
{
    use HasFactory;

    public const TYPE_DIRECT = 'direct';

    public const TYPE_ROOM = 'room';

    protected $fillable = [
        'type',
        'name',
        'avatar',
        'created_by_id',
        'last_message_at',
    ];

    protected function casts(): array
    {
        return [
            'last_message_at' => 'datetime',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'chat_conversation_user')
            ->withPivot('last_read_at', 'role')
            ->withTimestamps();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'conversation_id');
    }

    public function latestMessage(): HasOne
    {
        return $this->hasOne(ChatMessage::class, 'conversation_id')->latestOfMany();
    }

    public function isRoom(): bool
    {
        return $this->type === self::TYPE_ROOM;
    }

    public function isDirect(): bool
    {
        return $this->type === self::TYPE_DIRECT;
    }
}
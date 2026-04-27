<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

/**
 * Modelo User
 *
 * Representa um utilizador da aplicação Biblioteca.
 * Inclui métodos para permissões, relações com requisições, encomendas, reviews, alertas, mensagens e conversas de chat.
 *
 * Roles possíveis: admin, cidadao.
 */
class User extends Authenticatable
{
    use HasApiTokens;

    // Constantes para os tipos de utilizador
    public const ROLE_ADMIN = 'admin';
    public const ROLE_CIDADAO = 'cidadao';

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    // Campos que podem ser preenchidos em massa
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'estado',
        'delivery_nome',
        'delivery_morada',
        'delivery_codigo_postal',
        'delivery_localidade',
        'delivery_addresses',
        'stripe_customer_id',
        'saved_card',
        'cart_items_snapshot',
        'cart_updated_at',
        'cart_abandoned_notified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    // Campos ocultos quando o modelo é convertido em array ou JSON
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    // Atributos adicionais a incluir na serialização
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    // Casts automáticos de tipos para alguns campos
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'delivery_addresses' => 'array',
            'saved_card' => 'array',
            'cart_items_snapshot' => 'array',
            'cart_updated_at' => 'datetime',
            'cart_abandoned_notified_at' => 'datetime',
        ];
    }

    /**
     * Evento de criação: só permite criar Admins se já existir um admin autenticado ou se for o primeiro admin via consola.
     */
    protected static function booted(): void
    {
        static::creating(function (self $user): void {
            $user->role = $user->role ?: self::ROLE_CIDADAO;

            if ($user->role !== self::ROLE_ADMIN) {
                return;
            }

            $creator = auth()->user();

            if ($creator instanceof self && $creator->isAdmin()) {
                return;
            }

            // Apenas permite criar o primeiro Admin via consola (seed/bootstrap).
            if (app()->runningInConsole() && self::query()->where('role', self::ROLE_ADMIN)->doesntExist()) {
                return;
            }

            throw new AuthorizationException('Apenas Admin pode criar utilizadores Admin.');
        });
    }

    /**
     * Verifica se o utilizador tem um determinado papel (role).
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Verifica se o utilizador é admin.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole(self::ROLE_ADMIN);
    }

    /**
     * Verifica se o utilizador é cidadão.
     */
    public function isCidadao(): bool
    {
        return $this->hasRole(self::ROLE_CIDADAO);
    }

    /**
     * Relação: um utilizador pode ter várias requisições.
     */
    public function requisicoes(): HasMany
    {
        return $this->hasMany(Requisicao::class);
    }

    /**
     * Um utilizador pode ter muitos reviews
     */
    /**
     * Relação: um utilizador pode ter vários reviews.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Relação: um utilizador pode ter várias encomendas.
     */
    public function encomendas(): HasMany
    {
        return $this->hasMany(Encomenda::class);
    }

    /**
     * Relação: um utilizador pode ter vários alertas de livro.
     */
    public function alertasLivro()
    {
        return $this->hasMany(\App\Models\AlertaLivro::class);
    }

    /**
     * Relação: um utilizador pode ter várias mensagens de chat.
     */
    public function chatMessages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }

    /**
     * Relação: um utilizador pode ter várias conversas de chat criadas por si.
     */
    public function chatConversations(): HasMany
    {
        return $this->hasMany(ChatConversation::class, 'created_by_id');
    }

    /**
     * Relação: conversas em que o utilizador participa (muitos para muitos).
     */
    public function conversations()
    {
        return $this->belongsToMany(ChatConversation::class, 'chat_conversation_user')
            ->withTimestamps();
    }
}

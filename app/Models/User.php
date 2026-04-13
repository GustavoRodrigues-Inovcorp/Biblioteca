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

class User extends Authenticatable
{
    use HasApiTokens;

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
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
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
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
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

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(self::ROLE_ADMIN);
    }

    public function isCidadao(): bool
    {
        return $this->hasRole(self::ROLE_CIDADAO);
    }

    public function requisicoes(): HasMany
    {
        return $this->hasMany(Requisicao::class);
    }

    /**
     * Um utilizador pode ter muitos reviews
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function encomendas(): HasMany
    {
        return $this->hasMany(Encomenda::class);
    }

    public function alertasLivro()
    {
        return $this->hasMany(\App\Models\AlertaLivro::class);
    }
}

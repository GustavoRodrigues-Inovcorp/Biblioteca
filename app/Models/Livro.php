<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Livro extends Model
{
    /**
     * Sugere livros relacionados com base em palavras comuns na bibliografia.
     * Exclui o próprio livro.
     * @param int $limit
     * @return \Illuminate\Support\Collection
     */
    public function relacionados($limit = 4)
    {
        if (!$this->bibliografia)
            return collect();
        $palavras = collect(preg_split('/\W+/u', mb_strtolower($this->bibliografia)))->filter(fn($w) => mb_strlen($w) > 3)->unique();
        if ($palavras->isEmpty())
            return collect();

        $todos = Livro::where('id', '!=', $this->id)->get();
        $relacionados = $todos->map(function ($livro) use ($palavras) {
            if (!$livro->bibliografia)
                return ['livro' => $livro, 'score' => 0];
            $outras = collect(preg_split('/\W+/u', mb_strtolower($livro->bibliografia)))->filter(fn($w) => mb_strlen($w) > 3)->unique();
            $comum = $palavras->intersect($outras)->count();
            return ['livro' => $livro, 'score' => $comum];
        })->filter(fn($item) => $item['score'] > 0)
            ->sortByDesc('score')
            ->take(6)
            ->pluck('livro');
        return $relacionados;
    }

    protected $fillable = [
        'isbn',
        'nome',
        'editora_id',
        'bibliografia',
        'imagem_capa',
        'preco',
    ];

    protected $casts = [
        'preco' => 'decimal:2',
    ];

    /**
     * Relação com Editora (muitos livros para uma editora)
     */
    public function editora(): BelongsTo
    {
        return $this->belongsTo(Editora::class);
    }

    /**
     * Relação com Autores (muitos para muitos)
     * Requer tabela pivot: autor_livro
     */
    public function autores(): BelongsToMany
    {
        return $this->belongsToMany(Autor::class, 'autor_livro', 'livros_id', 'autores_id');
    }

    /**
     * Relação com Requisições (um livro pode ter várias ao longo do tempo).
     */
    public function requisicoes(): HasMany
    {
        return $this->hasMany(Requisicao::class);
    }

    /**
     * Requisições ativas (livro ainda não devolvido).
     */
    public function requisicoesAtivas(): HasMany
    {
        return $this->requisicoes()->whereNull('devolvido_em');
    }

    /**
     * Um livro está disponível quando não tem requisição ativa.
     */
    public function isDisponivel(): bool
    {
        return !$this->requisicoesAtivas()->exists();
    }

    /**
     * Relação com Reviews (um livro pode ter muitos reviews)
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Notifica os utilizadores que têm alertas para este livro quando ele fica disponível.
     */
    public function notificarAlertasDisponivel()
    {
        foreach ($this->alertas as $alerta) {
            \Mail::to($alerta->user->email)->send(new \App\Mail\LivroDisponivelMail($this));
            $alerta->delete();
        }
    }
    
    public function alertas()
    {
        return $this->hasMany(\App\Models\AlertaLivro::class);
    }
}

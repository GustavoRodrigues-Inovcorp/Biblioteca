<?php

namespace App\Services;

use App\Models\Livro;
use App\Models\Autor;
use App\Models\Editora;
use Illuminate\Support\Facades\DB;

class LivroImportService
{
    /**
     * Guarda um livro (e relações) a partir de dados da Google Books API.
     * Evita duplicados de livro (ISBN), autores (nome) e editora (nome).
     *
     * @param array $data
     * @return Livro|null
     */
    public function storeGoogleBook(array $data): ?Livro
    {
        return DB::transaction(function () use ($data) {
            // Editora
            $editora = null;
            if (!empty($data['editora'])) {
                $editora = Editora::firstOrCreate([
                    'nome' => $data['editora'],
                ]);
            }

            // Livro
            $livro = Livro::firstOrCreate(
                ['isbn' => $data['isbn']],
                [
                    'nome' => $data['nome'],
                    'editora_id' => $editora?->id,
                    'bibliografia' => $data['bibliografia'] ?? null,
                    'imagem_capa' => $data['imagem_capa'] ?? null,
                    'preco' => $data['preco'] ?? null,
                ]
            );

            // Autores
            if (!empty($data['autores'])) {
                $autorIds = [];
                foreach ($data['autores'] as $autorNome) {
                    $autor = Autor::firstOrCreate(['nome' => $autorNome]);
                    $autorIds[] = $autor->id;
                }
                $livro->autores()->sync($autorIds);
            }

            return $livro;
        });
    }
}

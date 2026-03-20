<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AutorLivroSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('autor_livro')->upsert(
            [
                ['id' => 1, 'autores_id' => 1, 'livros_id' => 1, 'created_at' => null, 'updated_at' => null],
                ['id' => 2, 'autores_id' => 1, 'livros_id' => 3, 'created_at' => null, 'updated_at' => null],
                ['id' => 3, 'autores_id' => 2, 'livros_id' => 2, 'created_at' => null, 'updated_at' => null],
                ['id' => 4, 'autores_id' => 3, 'livros_id' => 4, 'created_at' => null, 'updated_at' => null],
                ['id' => 5, 'autores_id' => 4, 'livros_id' => 5, 'created_at' => null, 'updated_at' => null],
                ['id' => 6, 'autores_id' => 5, 'livros_id' => 6, 'created_at' => null, 'updated_at' => null],
                ['id' => 7, 'autores_id' => 6, 'livros_id' => 7, 'created_at' => null, 'updated_at' => null],
                ['id' => 8, 'autores_id' => 7, 'livros_id' => 8, 'created_at' => null, 'updated_at' => null],
                ['id' => 9, 'autores_id' => 8, 'livros_id' => 9, 'created_at' => null, 'updated_at' => null],
                ['id' => 10, 'autores_id' => 10, 'livros_id' => 9, 'created_at' => null, 'updated_at' => null],
                ['id' => 11, 'autores_id' => 11, 'livros_id' => 9, 'created_at' => null, 'updated_at' => null],
                ['id' => 12, 'autores_id' => 9, 'livros_id' => 10, 'created_at' => null, 'updated_at' => null],
            ],
            ['id'],
            ['autores_id', 'livros_id', 'created_at', 'updated_at']
        );
    }
}
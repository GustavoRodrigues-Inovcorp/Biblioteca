<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EditorasSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('editoras')->upsert(
            [
                [
                    'id' => 1,
                    'nome' => 'TopSeller',
                    'logotipo' => 'editoras/topSeller.png',
                    'created_at' => '2026-03-17 10:30:01',
                    'updated_at' => '2026-03-17 10:30:01',
                ],
                [
                    'id' => 2,
                    'nome' => 'Berkley Books',
                    'logotipo' => 'editoras/berkleyBooks.jpg',
                    'created_at' => '2026-03-17 10:40:42',
                    'updated_at' => '2026-03-17 10:40:42',
                ],
                [
                    'id' => 3,
                    'nome' => 'Editorial Presença',
                    'logotipo' => 'editoras/editorialPresenca.jpeg',
                    'created_at' => '2026-03-17 10:48:39',
                    'updated_at' => '2026-03-17 10:48:39',
                ],
                [
                    'id' => 4,
                    'nome' => 'Delacorte Press',
                    'logotipo' => 'editoras/delacortePress.png',
                    'created_at' => '2026-03-17 10:59:49',
                    'updated_at' => '2026-03-17 10:59:49',
                ],
                [
                    'id' => 5,
                    'nome' => 'Simon & Schuster',
                    'logotipo' => 'editoras/simonAndSchuster.png',
                    'created_at' => '2026-03-17 11:01:50',
                    'updated_at' => '2026-03-17 11:01:50',
                ],
                [
                    'id' => 6,
                    'nome' => 'Bertrand',
                    'logotipo' => 'editoras/bertrand.jpg',
                    'created_at' => '2026-03-17 11:10:52',
                    'updated_at' => '2026-03-17 11:10:52',
                ],
                [
                    'id' => 7,
                    'nome' => 'Alma dos Livros',
                    'logotipo' => 'editoras/almadosLivros.jpg',
                    'created_at' => '2026-03-17 11:14:48',
                    'updated_at' => '2026-03-17 11:14:48',
                ],
                [
                    'id' => 8,
                    'nome' => 'Nuvem de Tinta',
                    'logotipo' => 'editoras/nuvemdeTinta.png',
                    'created_at' => '2026-03-17 11:17:40',
                    'updated_at' => '2026-03-17 11:17:40',
                ],
                [
                    'id' => 9,
                    'nome' => 'Edições Asa',
                    'logotipo' => 'editoras/edicoesAsa.png',
                    'created_at' => '2026-03-17 11:24:08',
                    'updated_at' => '2026-03-17 11:24:08',
                ],
            ],
            ['id'],
            ['nome', 'logotipo', 'created_at', 'updated_at']
        );
    }
}

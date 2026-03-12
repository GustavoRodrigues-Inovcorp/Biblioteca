<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EditorasSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('editoras')->upsert(
            array (
  0 => 
  array (
    'id' => 1,
    'nome' => 'Porto Editora',
    'logotipo' => 'https://cdn-shopkit.com/usercontent/twinklelittlestore/media/images/2c49ff1-092354-logo-porto-editora-cores-1.png',
    'created_at' => '2026-03-11 11:17:33',
    'updated_at' => '2026-03-11 11:17:33',
  ),
  1 => 
  array (
    'id' => 2,
    'nome' => 'Leya',
    'logotipo' => 'https://www.leya.com/asset/img/LeYa-logo.png',
    'created_at' => '2026-03-11 11:17:33',
    'updated_at' => '2026-03-11 11:17:33',
  ),
  2 => 
  array (
    'id' => 3,
    'nome' => 'Presença',
    'logotipo' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS4THBQRbo1tNsOT7wITQ1YJzydUfBmjoFWZg&s',
    'created_at' => '2026-03-11 11:17:33',
    'updated_at' => '2026-03-11 11:17:33',
  ),
  3 => 
  array (
    'id' => 4,
    'nome' => 'Bertrand',
    'logotipo' => 'https://www.forumalgarve.net/wp-content/uploads/sites/12/2021/06/Bertrand-Forum-Algarve-Faro-Original.png',
    'created_at' => '2026-03-11 11:17:33',
    'updated_at' => '2026-03-11 11:17:33',
  ),
  4 => 
  array (
    'id' => 5,
    'nome' => 'Alfaguara',
    'logotipo' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRgM0FaJmaE279_hZMeZyKm7QvDF29PPsLIdg&s',
    'created_at' => '2026-03-11 11:17:33',
    'updated_at' => '2026-03-11 11:17:33',
  ),
  5 => 
  array (
    'id' => 6,
    'nome' => 'Relógio D\'Agua',
    'logotipo' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQTPpn2atDctXZyyfjFb1Zz0MEvRj2MRsIeEA&s',
    'created_at' => '2026-03-11 11:17:33',
    'updated_at' => '2026-03-11 11:17:33',
  ),
  6 => 
  array (
    'id' => 7,
    'nome' => 'Dom Quixote',
    'logotipo' => 'https://dynamic-media-cdn.tripadvisor.com/media/photo-o/28/b0/b9/bd/cervejaria-d-quixote.jpg?w=800&h=500&s=1',
    'created_at' => '2026-03-11 11:17:33',
    'updated_at' => '2026-03-11 11:17:33',
  ),
  7 => 
  array (
    'id' => 8,
    'nome' => 'Companhia das Letras',
    'logotipo' => 'https://yt3.googleusercontent.com/CRYo-EaU_Pa7lt1wWId5iLAJnvHmZci4ab0ZgKAjH5DeARr2Yg_h7pNzx0YbtU4MUfmqNvnU=s900-c-k-c0x00ffffff-no-rj',
    'created_at' => '2026-03-11 11:17:33',
    'updated_at' => '2026-03-11 11:17:33',
  ),
  8 => 
  array (
    'id' => 9,
    'nome' => 'Penguin Random House',
    'logotipo' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSzw-Y5DeaoFHAz2gdPIUuB8L1OoP31YERYng&s',
    'created_at' => '2026-03-11 11:17:33',
    'updated_at' => '2026-03-11 11:17:33',
  ),
  9 => 
  array (
    'id' => 10,
    'nome' => 'HarperCollins',
    'logotipo' => 'https://media.licdn.com/dms/image/v2/C560BAQH4-dG3X99t_Q/company-logo_200_200/company-logo_200_200/0/1631378988391?e=2147483647&v=beta&t=kVDV0-oeiQCZuSvJhqrixXZWPRg0XsRIk71IRHwVWW8',
    'created_at' => '2026-03-11 11:17:33',
    'updated_at' => '2026-03-11 11:17:33',
  ),
  10 => 
  array (
    'id' => 11,
    'nome' => 'Bloomsbury',
    'logotipo' => 'https://i.ytimg.com/vi/5m6S0N8zDN8/hqdefault.jpg',
    'created_at' => '2026-03-11 11:17:33',
    'updated_at' => '2026-03-11 11:17:33',
  ),
  11 => 
  array (
    'id' => 12,
    'nome' => 'Record',
    'logotipo' => 'https://cdn.record.com.br/wp-content/uploads/2019/08/25181714/record.png',
    'created_at' => '2026-03-11 11:17:33',
    'updated_at' => '2026-03-11 11:17:33',
  ),
  12 => 
  array (
    'id' => 13,
    'nome' => 'Gradiva',
    'logotipo' => 'https://www.gradiva.pt/wp-content/uploads/2024/12/Logo-Gradiva.png',
    'created_at' => '2026-03-11 11:17:33',
    'updated_at' => '2026-03-11 11:17:33',
  ),
),
            ['id'],
            array (
  0 => 'nome',
  1 => 'logotipo',
  2 => 'created_at',
  3 => 'updated_at',
)
        );
    }
}

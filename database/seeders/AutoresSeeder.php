<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AutoresSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('autores')->upsert(
            array (
  0 => 
  array (
    'id' => 1,
    'nome' => 'José Saramago',
    'foto' => 'https://upload.wikimedia.org/wikipedia/commons/8/87/JSJoseSaramago_%28cropped%29.jpg',
    'created_at' => '2026-03-11 11:17:39',
    'updated_at' => '2026-03-11 11:17:39',
  ),
  1 => 
  array (
    'id' => 2,
    'nome' => 'Fernando Pessoa',
    'foto' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/32/Pessoa_chapeu.jpg/330px-Pessoa_chapeu.jpg',
    'created_at' => '2026-03-11 11:17:40',
    'updated_at' => '2026-03-11 11:17:40',
  ),
  2 => 
  array (
    'id' => 3,
    'nome' => 'Eça de Queirós',
    'foto' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/d/d9/E%C3%A7a_de_Queir%C3%B3s_c._1882.jpg/330px-E%C3%A7a_de_Queir%C3%B3s_c._1882.jpg',
    'created_at' => '2026-03-11 11:17:44',
    'updated_at' => '2026-03-11 11:17:44',
  ),
  3 => 
  array (
    'id' => 4,
    'nome' => 'Mia Couto',
    'foto' => 'https://upload.wikimedia.org/wikipedia/commons/6/66/Mia_Couto_cropped.jpg',
    'created_at' => '2026-03-11 11:17:45',
    'updated_at' => '2026-03-11 11:17:45',
  ),
  4 => 
  array (
    'id' => 5,
    'nome' => 'Clarice Lispector',
    'foto' => 'https://upload.wikimedia.org/wikipedia/commons/7/7c/%281920-1977%29_Clarice_Lispector_6zxkp_please_credit%28palette.fm%29_%28cropped%29.png',
    'created_at' => '2026-03-11 11:17:46',
    'updated_at' => '2026-03-11 11:17:46',
  ),
  5 => 
  array (
    'id' => 6,
    'nome' => 'Machado de Assis',
    'foto' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/ef/Machado_de_Assis_by_Marc_Ferrez.jpg/330px-Machado_de_Assis_by_Marc_Ferrez.jpg',
    'created_at' => '2026-03-11 11:17:48',
    'updated_at' => '2026-03-11 11:17:48',
  ),
  6 => 
  array (
    'id' => 7,
    'nome' => 'Jorge Amado',
    'foto' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/89/Jorge_Amado%2C_Headshot_2%2C_1988_%28cropped%29.png/330px-Jorge_Amado%2C_Headshot_2%2C_1988_%28cropped%29.png',
    'created_at' => '2026-03-11 11:17:49',
    'updated_at' => '2026-03-11 11:17:49',
  ),
  7 => 
  array (
    'id' => 8,
    'nome' => 'Camilo Castelo Branco',
    'foto' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/05/Camilo_Castelo_Branco_Bohemia_do_Espirito.png/330px-Camilo_Castelo_Branco_Bohemia_do_Espirito.png',
    'created_at' => '2026-03-11 11:17:51',
    'updated_at' => '2026-03-11 11:17:51',
  ),
  8 => 
  array (
    'id' => 9,
    'nome' => 'Agustina Bessa-Luis',
    'foto' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/00/Agustina_Bessa-Lu%C3%ADs_%28Converg%C3%AAncia%2C_1969%29_-_Arquivos_RTP.png/330px-Agustina_Bessa-Lu%C3%ADs_%28Converg%C3%AAncia%2C_1969%29_-_Arquivos_RTP.png',
    'created_at' => '2026-03-11 11:17:55',
    'updated_at' => '2026-03-11 11:17:55',
  ),
  9 => 
  array (
    'id' => 10,
    'nome' => 'Valter Hugo Mãe',
    'foto' => 'https://media.assettype.com/dn/2025-06-22/qtgqo9a1/DN18062025REINALDORODRIGUES106.JPG?w=1200&h=675&auto=format%2Ccompress&fit=max&enlarge=true',
    'created_at' => '2026-03-11 11:17:57',
    'updated_at' => '2026-03-11 11:17:57',
  ),
  10 => 
  array (
    'id' => 11,
    'nome' => 'José Rodrigues dos Santos',
    'foto' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/f4/Jos%C3%A9_Rodrigues_dos_Santos_%282024%29.png/330px-Jos%C3%A9_Rodrigues_dos_Santos_%282024%29.png',
    'created_at' => '2026-03-11 11:18:00',
    'updated_at' => '2026-03-11 11:18:00',
  ),
  11 => 
  array (
    'id' => 12,
    'nome' => 'Afonso Cruz',
    'foto' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/4/4f/Afonso_Cruz_%282019%29.jpg/330px-Afonso_Cruz_%282019%29.jpg',
    'created_at' => '2026-03-11 11:18:01',
    'updated_at' => '2026-03-11 11:18:01',
  ),
  12 => 
  array (
    'id' => 13,
    'nome' => 'Sophia de Mello Breyner',
    'foto' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/fb/Sophia_Mello_Breyner_Andersen.png/330px-Sophia_Mello_Breyner_Andersen.png',
    'created_at' => '2026-03-11 11:18:03',
    'updated_at' => '2026-03-11 11:18:03',
  ),
  13 => 
  array (
    'id' => 14,
    'nome' => 'J. K. Rowling',
    'foto' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/5d/J._K._Rowling_2010.jpg/330px-J._K._Rowling_2010.jpg',
    'created_at' => '2026-03-11 11:18:04',
    'updated_at' => '2026-03-11 11:18:04',
  ),
  14 => 
  array (
    'id' => 15,
    'nome' => 'J. R. R. Tolkien',
    'foto' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/d/d4/J._R._R._Tolkien%2C_ca._1925.jpg/330px-J._R._R._Tolkien%2C_ca._1925.jpg',
    'created_at' => '2026-03-11 11:18:05',
    'updated_at' => '2026-03-11 11:18:05',
  ),
  15 => 
  array (
    'id' => 16,
    'nome' => 'George Orwell',
    'foto' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/7e/George_Orwell_press_photo.jpg/330px-George_Orwell_press_photo.jpg',
    'created_at' => '2026-03-11 11:18:06',
    'updated_at' => '2026-03-11 11:18:06',
  ),
),
            ['id'],
            array (
  0 => 'nome',
  1 => 'foto',
  2 => 'created_at',
  3 => 'updated_at',
)
        );
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AutoresSeeder extends Seeder
{
  public function run(): void
  {
    DB::table('autores')->upsert(
      array(
        0 =>
          array(
            'id' => 1,
            'nome' => 'Colleen Hoover',
            'foto' => 'autores/ColleenHoover.webp',
            'created_at' => '2026-03-17 11:53:39',
            'updated_at' => '2026-03-17 11:53:39',
          ),
        1 =>
          array(
            'id' => 2,
            'nome' => 'Ali Hazelwood',
            'foto' => 'autores/AliHazelwood.webp',
            'created_at' => '2026-03-17 11:53:39',
            'updated_at' => '2026-03-17 11:53:39',
          ),
        2 =>
          array(
            'id' => 3,
            'nome' => 'Karen M. McManus',
            'foto' => 'autores/KarenMMcManus.webp',
            'created_at' => '2026-03-17 11:53:39',
            'updated_at' => '2026-03-17 11:53:39',
          ),
        3 =>
          array(
            'id' => 4,
            'nome' => 'Hannah Grace',
            'foto' => 'autores/HannahGrace.jpg',
            'created_at' => '2026-03-17 11:53:39',
            'updated_at' => '2026-03-17 11:53:39',
          ),
        4 =>
          array(
            'id' => 5,
            'nome' => 'Freida McFadden',
            'foto' => 'autores/FreidaMcFadden.webp',
            'created_at' => '2026-03-17 11:53:39',
            'updated_at' => '2026-03-17 11:53:39',
          ),
        5 =>
          array(
            'id' => 6,
            'nome' => 'Jennifer Niven',
            'foto' => 'autores/JenniferNiven.jpg',
            'created_at' => '2026-03-17 11:53:39',
            'updated_at' => '2026-03-17 11:53:39',
          ),
        6 =>
          array(
            'id' => 7,
            'nome' => 'Lynn Painter',
            'foto' => 'autores/LynnPainter.webp',
            'created_at' => '2026-03-17 11:53:39',
            'updated_at' => '2026-03-17 11:53:39',
          ),
        7 =>
          array(
            'id' => 8,
            'nome' => 'Rachael Lippincott',
            'foto' => 'autores/RachaelLippincott.jpg',
            'created_at' => '2026-03-17 11:53:39',
            'updated_at' => '2026-03-17 11:53:39',
          ),
        8 =>
          array(
            'id' => 9,
            'nome' => 'Mia Sheridan',
            'foto' => 'autores/MiaSheridan.jpg',
            'created_at' => '2026-03-17 11:53:39',
            'updated_at' => '2026-03-17 11:53:39',
          ),
        9 =>
          array(
            'id' => 10,
            'nome' => 'Mikki Daughtry',
            'foto' => 'autores/MikkiDaughtry.jpg',
            'created_at' => '2026-03-17 11:53:39',
            'updated_at' => '2026-03-17 11:53:39',
          ),
        10 =>
          array(
            'id' => 11,
            'nome' => 'Tobias Iaconis',
            'foto' => 'autores/TobiasIaconis.jpg',
            'created_at' => '2026-03-17 11:53:39',
            'updated_at' => '2026-03-17 11:53:39',
          ),
      ),
      ['id'],
      array(
        0 => 'nome',
        1 => 'foto',
        2 => 'created_at',
        3 => 'updated_at',
      )
    );
  }
}

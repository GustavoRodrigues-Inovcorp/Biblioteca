<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('password'),
                'role' => User::ROLE_ADMIN,
                'email_verified_at' => now(),
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'User',
                'password' => bcrypt('password'),
                'role' => User::ROLE_CIDADAO,
                'email_verified_at' => now(),
            ]
        );

        Schema::disableForeignKeyConstraints();
        DB::table('autor_livro')->truncate();
        DB::table('livros')->truncate();
        DB::table('autores')->truncate();
        DB::table('editoras')->truncate();
        Schema::enableForeignKeyConstraints();

        $this->call([
            EditorasSeeder::class,
            AutoresSeeder::class,
            LivrosSeeder::class,
            AutorLivroSeeder::class,
        ]);
    }
}

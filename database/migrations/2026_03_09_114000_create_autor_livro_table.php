<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('autor_livro', function (Blueprint $table) {
            $table->id();
            $table->foreignId('autores_id')->constrained('autores')->onDelete('cascade');
            $table->foreignId('livros_id')->constrained('livros')->onDelete('cascade');
            $table->timestamps();
            
            // Evitar duplicatas
            $table->unique(['autores_id', 'livros_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('autor_livro');
    }
};

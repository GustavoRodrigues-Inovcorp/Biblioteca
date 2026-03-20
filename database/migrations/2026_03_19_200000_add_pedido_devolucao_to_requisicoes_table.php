<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('requisicoes', function (Blueprint $table) {
            $table->timestamp('pedido_devolucao_em')->nullable()->after('devolvido_em');
            $table->string('estado_devolucao')->nullable()->after('pedido_devolucao_em'); // pendente, aceite, recusado
        });
    }

    public function down(): void
    {
        Schema::table('requisicoes', function (Blueprint $table) {
            $table->dropColumn(['pedido_devolucao_em', 'estado_devolucao']);
        });
    }
};

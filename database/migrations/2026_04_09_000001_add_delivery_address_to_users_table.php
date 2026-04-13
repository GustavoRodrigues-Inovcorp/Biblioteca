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
        Schema::table('users', function (Blueprint $table): void {
            $table->string('delivery_nome')->nullable()->after('profile_photo_path');
            $table->string('delivery_morada')->nullable()->after('delivery_nome');
            $table->string('delivery_codigo_postal')->nullable()->after('delivery_morada');
            $table->string('delivery_localidade')->nullable()->after('delivery_codigo_postal');
            $table->string('delivery_telefone')->nullable()->after('delivery_localidade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn([
                'delivery_nome',
                'delivery_morada',
                'delivery_codigo_postal',
                'delivery_localidade',
                'delivery_telefone',
            ]);
        });
    }
};

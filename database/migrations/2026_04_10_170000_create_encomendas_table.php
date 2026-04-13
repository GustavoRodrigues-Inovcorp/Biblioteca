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
        Schema::create('encomendas', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('numero')->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('total', 10, 2);
            $table->unsignedInteger('total_itens')->default(0);
            $table->string('payment_method', 40);
            $table->string('payment_status', 20)->index();
            $table->string('stripe_payment_intent_id')->nullable()->index();
            $table->string('stripe_checkout_session_id')->nullable()->index();
            $table->timestamp('paid_at')->nullable();
            $table->json('itens')->nullable();
            $table->json('morada_entrega')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('encomendas');
    }
};

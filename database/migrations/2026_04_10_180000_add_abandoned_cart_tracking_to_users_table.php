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
            $table->json('cart_items_snapshot')->nullable()->after('saved_card');
            $table->timestamp('cart_updated_at')->nullable()->after('cart_items_snapshot');
            $table->timestamp('cart_abandoned_notified_at')->nullable()->after('cart_updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn([
                'cart_items_snapshot',
                'cart_updated_at',
                'cart_abandoned_notified_at',
            ]);
        });
    }
};

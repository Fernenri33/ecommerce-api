<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('cart_id')->constrained('carts');
            // (opcional) totales si los vas a usar:
            // $table->unsignedBigInteger('subtotal')->default(0);
            // $table->unsignedBigInteger('discount_total')->default(0);
            // $table->unsignedBigInteger('tax_total')->default(0);
            // $table->unsignedBigInteger('grand_total')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->unique('cart_id', 'ux_orders_cart'); // idempotencia
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products'); // denormalizado para joins rápidos
            $table->foreignId('price_id')->constrained('prices');     // snapshot del price usado

            $table->unsignedInteger('quantity')->default(1);          // cantidad del "price" (paquete)
            $table->unsignedBigInteger('unit_price');                 // centavos del price en el momento
            $table->char('currency', 3)->default('USD');

            // (opcional) si quieres snapshot del descuento aplicado:
            // $table->foreignId('discount_id')->nullable()->constrained('discounts');

            $table->timestamps();
            $table->softDeletes();

            // Evita duplicar el mismo price en la misma orden; si prefieres permitir duplicados, comenta esto.
            $table->unique(['order_id', 'price_id'], 'ux_orderitems_order_price');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};

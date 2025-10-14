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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string("name",100)->unique();;
            $table->string("sku",32)->nullable()->unique();
            $table->text("description")->nullable();
            $table->string("imagen",255);
            $table->integer("available_quantity");
            $table->integer("warehouse_quantity");
            $table->foreignId("unit_id")->constrained("units");
            $table->enum('status', ['active', 'inactive', 'hidden'])->default('active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

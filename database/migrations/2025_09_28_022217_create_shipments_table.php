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
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_address_id")->constrained("user_addresses");
            $table->foreignId("oreder_id")->constrained("orders");
            $table->enum('status', ['pending', 'delivered', 'on_the_way'])->default('pending');
            $table->foreignId("courier")->nullable()->constrained("users");
            $table->dateTime('delivery_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};

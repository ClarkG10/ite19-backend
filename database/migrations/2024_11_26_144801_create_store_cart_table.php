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
        Schema::create('store_cart', function (Blueprint $table) {
            $table->id('storecart_id');
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->timestamps();
        });

        Schema::create('reorder_requests', function ($table) {
            $table->unsignedBigInteger('product_id ');
            $table->foreign('product_id ')->references('product_id ')->on('products');
        });

        Schema::create('reorder_requests', function ($table) {
            $table->unsignedBigInteger('store_id ');
            $table->foreign('store_id ')->references('id ')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_cart');
    }
};

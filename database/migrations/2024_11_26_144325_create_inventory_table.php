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
        Schema::create('inventory', function (Blueprint $table) {
            $table->id('inventory_id');
            $table->integer('quantity');
            $table->decimal('new_price', 10, 2);
            $table->integer('reorder_level');
            $table->integer('reorder_quantity');
            $table->timestamps();
        });

        Schema::create('inventory', function ($table) {
            $table->unsignedBigInteger('store_id');
            $table->foreign('store_id')->references('id')->on('users');
        });

        Schema::create('inventory', function ($table) {
            $table->unsignedBigInteger('product_id ');
            $table->foreign('product_id ')->references('product_id ')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory');
    }
};

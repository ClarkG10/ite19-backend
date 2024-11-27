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
        Schema::create('reorder_requests', function (Blueprint $table) {
            $table->id('reorder_id');
            $table->integer('quantity');
            $table->string('status')->default('Pending');
            $table->date('shipped_date');
            $table->date('delivered_date');
            $table->timestamps();
        });
        Schema::create('reorder_requests', function ($table) {
            $table->unsignedBigInteger('store_id');
            $table->foreign('store_id')->references('id')->on('users');
        });

        Schema::create('reorder_requests', function ($table) {
            $table->unsignedBigInteger('vendor_id');
            $table->foreign('vendor_id')->references('id')->on('users');
        });

        Schema::create('reorder_requests', function ($table) {
            $table->unsignedBigInteger('product_id ');
            $table->foreign('product_id ')->references('product_id ')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reorder_requests');
    }
};

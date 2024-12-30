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
        Schema::create('orders', function (Blueprint $table) {
            $table->id('order_id');
            $table->string('status')->default('Pending');
            $table->string('shipping_address');
            $table->integer('shipping_cost');
            $table->date('shipped_date')->nullable();
            $table->date('delivered_date')->nullable();
            $table->string('payment_method')->default("Cash On Delivery")->nullable();
            $table->decimal('total_amount', 10, 2);
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('store_id');
            $table->unsignedBigInteger('cart_id');
            $table->foreign('cart_id')->references('id')->on('carts');
            $table->foreign('customer_id')->references('customer_id')->on('customers');
            $table->foreign('store_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

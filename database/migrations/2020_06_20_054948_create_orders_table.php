<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->foreign('customer_id')->references('id')->on('user');
            $table->unsignedBigInteger('billing_address');
            $table->foreign('billing_address')->references('id')->on('user_billing_address');
            $table->unsignedBigInteger('shipping_address');
            $table->foreign('shipping_address')->references('id')->on('user_shipping_address');
            $table->integer('shipping_amount');
            $table->integer('discount');
            $table->integer('sub_total');
            $table->integer('total');
            $table->integer('tax_amount');
            $table->string('status');
            $table->string('transaction_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}

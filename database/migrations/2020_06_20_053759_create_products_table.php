<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_type');
            $table->foreign('product_type')->references('id')->on('products_type');
            $table->string('name');
            $table->string('short_description');
            $table->longText('description');
            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')->references('id')->on('product_categories');
            $table->integer('price');
            $table->boolean('in_stock');
            $table->integer('quantity');
            $table->text('product_images');
			$table->text('child_products');
			$table->text('related_products');
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
        Schema::dropIfExists('products');
    }
}

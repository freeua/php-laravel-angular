<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->increments('id');
            $table->string('code', 12)->nullable();
            $table->string('description')->nullable();
            $table->string('image')->nullable();
            $table->integer('supplier_id')->unsigned();
            $table->foreign('supplier_id')->references('id')->on('suppliers');
            $table->integer('model_id')->unsigned();
            $table->foreign('model_id')->references('id')->on('product_models');
            $table->integer('brand_id')->unsigned();
            $table->foreign('brand_id')->references('id')->on('product_brands');
            $table->integer('category_id')->unsigned();
            $table->foreign('category_id')->references('id')->on('product_categories');
            $table->timestamps();
            $table->softDeletes();
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

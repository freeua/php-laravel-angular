<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOfferAccessoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offer_accessories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('unit_id')->unsigned();
            $table->foreign('unit_id')->on('units')->references('id');
            $table->integer('offer_id')->unsigned();
            $table->foreign('offer_id')->on('offers')->references('id');
            $table->decimal('amount');
            $table->decimal('price');
            $table->decimal('tax', 5, 2);
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
        Schema::dropIfExists('offer_accessories');
    }
}

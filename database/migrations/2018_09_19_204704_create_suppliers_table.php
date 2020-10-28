<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 10);
            $table->string('logo')->nullable();
            $table->string('color', 10)->nullable();
            $table->string('shop_name')->nullable();
            $table->integer('city_id')->unsigned();
            $table->foreign('city_id')->references('id')->on('cities');
            $table->string('name');
            $table->string('admin_first_name');
            $table->string('admin_last_name');
            $table->string('admin_email');
            $table->string('address');
            $table->string('zip', 20)->nullable();
            $table->string('vat', 20);
            $table->string('phone');
            $table->integer('status_id')->unsigned();
            $table->foreign('status_id')->references('id')->on('statuses');
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
        Schema::dropIfExists('suppliers');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTechnicalToOrdersOffersContractsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->integer('current_technical_service_id')->unsigned()->nullable();
            $table->foreign('current_technical_service_id')->references('id')->on('technical_services');
        });
        Schema::table('offers', function (Blueprint $table) {
            $table->integer('current_technical_service_id')->unsigned()->nullable();
            $table->foreign('current_technical_service_id')->references('id')->on('technical_services');
        });
        Schema::table('contracts', function (Blueprint $table) {
            $table->integer('current_technical_service_id')->unsigned()->nullable();
            $table->foreign('current_technical_service_id')->references('id')->on('technical_services');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['current_technical_service_id']);
            $table->dropColumn('current_technical_service_id');
        });
        Schema::table('offers', function (Blueprint $table) {
            $table->dropForeign(['current_technical_service_id']);
            $table->dropColumn('current_technical_service_id');
        });
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropForeign(['current_technical_service_id']);
            $table->dropColumn('current_technical_service_id');
        });
    }
}

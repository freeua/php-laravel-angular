<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateReportTables
 */
class CreateReportTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('reports', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->text('body');
            $table->timestamps();
        });

        Schema::create('report_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });

        Schema::create('report_report_category', function (Blueprint $table) {
            $table->integer('report_id')->unsigned();
            $table->integer('report_category_id')->unsigned();
            $table->foreign('report_id')->references('id')->on('reports')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('report_category_id')->references('id')->on('report_categories')
                ->onDelete('cascade')->onUpdate('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('report_report_category');
        Schema::dropIfExists('report_categories');
    }
}

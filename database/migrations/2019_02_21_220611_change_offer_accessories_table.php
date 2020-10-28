<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeOfferAccessoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Schema::table('offer_accessories', function(Blueprint $table) {
            $table->dropForeign('offer_accessories_unit_id_foreign');
            $table->dropColumn('unit_id');
            $table->renameColumn('tax', 'discount');
        });
        \Schema::drop('units');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \Schema::table('offer_accessories', function(Blueprint $table) {
            $table->unsignedInteger('unit_id');
            $table->foreign('unit_id')->on('units')->references('id');
            $table->renameColumn('discount', 'tax');
        });
        \Schema::create('units', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });
    }
}

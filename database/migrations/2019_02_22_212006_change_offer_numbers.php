<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeOfferNumbers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Schema::table('offers', function (Blueprint $table) {
            $table->float('bike_list_price')->change();
            $table->float('bike_discounted_price')->change();
            $table->float('accessories_price')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \Schema::table('offers', function (Blueprint $table) {
            $table->decimal('bike_list_price')->change();
            $table->decimal('bike_discounted_price')->change();
            $table->decimal('accessories_price')->change();
        });
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMaximumServiceYearToServiceRatesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('service_rates', function (Blueprint $table) {
            $table->integer('maximum_service_year')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('service_rates', function (Blueprint $table) {
            $table->dropColumn('maximum_service_year');
        });
    }
}

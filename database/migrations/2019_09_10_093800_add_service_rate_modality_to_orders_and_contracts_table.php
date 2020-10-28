<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddServiceRateModalityToOrdersAndContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->text('service_rate_modality')->nullable();
        });
        Schema::table('contracts', function (Blueprint $table) {
            $table->text('service_rate_modality')->nullable();
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
            $table->dropColumn('service_rate_modality');
        });
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn('service_rate_modality');
        });
    }
}

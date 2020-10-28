<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOfferPriceFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Schema::table('offers', function (Blueprint $table) {
            $table->float('leasing_rate_amount')->after('product_list_price');
            $table->float('insurance_rate_amount')->after('product_list_price');
            $table->float('service_rate_amount')->after('product_list_price');
            $table->float('leasing_rate_subsidy')->after('product_list_price');
            $table->float('insurance_rate_subsidy')->after('product_list_price');
            $table->float('service_rate_subsidy')->after('product_list_price');
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
            $table->dropColumn('leasing_rate_amount');
            $table->dropColumn('insurance_rate_amount');
            $table->dropColumn('service_rate_amount');
            $table->dropColumn('leasing_rate_subsidy');
            $table->dropColumn('insurance_rate_subsidy');
            $table->dropColumn('service_rate_subsidy');
        });
    }
}

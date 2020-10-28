<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRateAmountColumns extends Migration
{
    public function up()
    {
        \Schema::table('orders', function (Blueprint $table) {
            $table->string('service_rate_amount')->after('service_rate_name');
            $table->string('insurance_rate_amount')->after('insurance_rate_name');
            $table->string('leasing_rate_amount')->after('leasing_rate');
        });
        \Schema::table('contracts', function (Blueprint $table) {
            $table->string('service_rate_amount')->after('service_rate_name');
            $table->string('insurance_rate_amount')->after('insurance_rate_name');
            $table->string('leasing_rate_amount')->after('leasing_rate');
        });
    }

    public function down()
    {
        \Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('service_rate_amount');
            $table->dropColumn('insurance_rate_amount');
            $table->dropColumn('leasing_rate_amount');
        });
        \Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn('service_rate_amount');
            $table->dropColumn('insurance_rate_amount');
            $table->dropColumn('leasing_rate_amount');
        });
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeOrderFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Schema::table('orders', function (Blueprint $table) {
            $table->float('list_price')->change();
            $table->renameColumn('list_price', 'bike_list_price');
            $table->float('bike_discounted_price')->after('list_price');
            $table->float('bike_discount')->after('list_price');
            $table->float('accessories_price')->after('list_price');
            $table->float('accessories_discounted_price')->after('list_price');
            $table->string('insurance_rate_name')->after('list_price');
            $table->string('service_rate_name')->after('list_price');
            $table->float('leasing_rate')->change();
            $table->float('insurance_rate')->change();
            $table->float('service_rate')->change();
            $table->float('leasing_rate_subsidy')->change();
            $table->float('insurance_rate_subsidy')->change();
            $table->float('calculated_residual_value')->change();
            $table->float('agreed_purchase_price')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \Schema::table('orders', function (Blueprint $table) {
            $table->decimal('bike_list_price')->change();
            $table->renameColumn('bike_list_price', 'list_price');
            $table->removeColumn('bike_discounted_price');
            $table->removeColumn('bike_discount');
            $table->removeColumn('accessories_price');
            $table->removeColumn('accessories_discounted_price');
            $table->removeColumn('insurance_rate_name');
            $table->removeColumn('service_rate_name');
            $table->decimal('bike_list_price')->change();
            $table->decimal('leasing_rate')->change();
            $table->decimal('insurance_rate')->change();
            $table->decimal('service_rate')->change();
            $table->decimal('leasing_rate_subsidy')->change();
            $table->decimal('insurance_rate_subsidy')->change();
            $table->decimal('calculated_residual_value')->change();
            $table->decimal('agreed_purchase_price')->change();
        });
    }
}

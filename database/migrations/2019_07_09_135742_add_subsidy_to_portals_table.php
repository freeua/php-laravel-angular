<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSubsidyToPortalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('portals', function (Blueprint $table) {
            $table->tinyInteger('insurance_rate_subsidy')->default(0);
            $table->string('insurance_rate_subsidy_type')->default('percentage');
            $table->double('insurance_rate_subsidy_amount', 10, 2)->default(100);
            $table->tinyInteger('service_rate_subsidy')->default(0);
            $table->string('service_rate_subsidy_type')->default('percentage');
            $table->double('service_rate_subsidy_amount', 10, 2)->default(100);
            $table->tinyInteger('leasing_rate_subsidy')->default(0);
            $table->string('leasing_rate_subsidy_type')->default('percentage');
            $table->double('leasing_rate_subsidy_amount', 10, 2)->default(100);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('portals', function (Blueprint $table) {
            $table->dropColumn('insurance_rate_subsidy');
            $table->dropColumn('insurance_rate_subsidy_type');
            $table->dropColumn('insurance_rate_subsidy_amount');
            $table->dropColumn('service_rate_subsidy');
            $table->dropColumn('service_rate_subsidy_type');
            $table->dropColumn('service_rate_subsidy_amount');
            $table->dropColumn('leasing_rate_subsidy');
            $table->dropColumn('leasing_rate_subsidy_type');
            $table->dropColumn('leasing_rate_subsidy_amount');
        });
    }
}

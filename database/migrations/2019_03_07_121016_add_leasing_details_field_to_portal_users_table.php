<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLeasingDetailsFieldToPortalUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('portal_users', function (Blueprint $table) {
            $table->tinyInteger('insurance_rate_subsidy')->default(0);
            $table->string('insurance_rate_subsidy_type')->nullable();
            $table->double('insurance_rate_subsidy_amount', 10, 2)->nullable();
            $table->tinyInteger('service_rate_subsidy')->default(0);
            $table->string('service_rate_subsidy_type')->nullable();
            $table->double('service_rate_subsidy_amount', 10, 2)->nullable();
            $table->tinyInteger('leasing_rate_subsidy')->default(0);
            $table->string('leasing_rate_subsidy_type')->nullable();
            $table->double('leasing_rate_subsidy_amount', 10, 2)->nullable();
            $table->integer('max_user_contracts')->unsigned()->nullable();
            $table->double('max_user_amount', 10, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('portal_users', function (Blueprint $table) {
            $table->removeColumn('insurance_rate_subsidy');
            $table->removeColumn('insurance_rate_subsidy_type');
            $table->removeColumn('insurance_rate_subsidy_amount');
            $table->removeColumn('service_rate_subsidy');
            $table->removeColumn('service_rate_subsidy_type');
            $table->removeColumn('service_rate_subsidy_amount');
            $table->removeColumn('leasing_rate_subsidy');
            $table->removeColumn('leasing_rate_subsidy_type');
            $table->removeColumn('leasing_rate_subsidy_amount');
            $table->removeColumn('max_user_contracts');
            $table->removeColumn('max_user_amount');
        });
    }
}

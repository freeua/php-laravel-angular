<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeServiceBudgetContract extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('service_rates', function (Blueprint $table) {
            $table->renameColumn('yearly_budget', 'budget');
        });
        Schema::table('contracts', function (Blueprint $table) {
            $table->renameColumn('service_yearly_budget', 'service_budget');
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
            $table->renameColumn('budget', 'yearly_budget');
        });
        Schema::table('contract', function (Blueprint $table) {
            $table->renameColumn('service_budget', 'service_yearly_budget');
        });
    }
}

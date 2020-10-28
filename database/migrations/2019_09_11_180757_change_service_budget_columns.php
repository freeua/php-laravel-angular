<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeServiceBudgetColumns extends Migration
{

    public function up()
    {
        Schema::table('service_rates', function (Blueprint $table) {
            $table->renameColumn('maximum_service_year', 'yearly_budget');
        });
        Schema::table('contracts', function (Blueprint $table) {
            $table->double('service_yearly_budget');
        });
        DB::table('contracts')->update(['service_yearly_budget' => 0]);
        DB::table('service_rates')->update(['yearly_budget' => 0]);
    }

    public function down()
    {
        //
    }
}

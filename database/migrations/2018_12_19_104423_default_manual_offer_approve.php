<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class DefaultManualOfferApprove extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('manual_contract_approval');
        });
        \Schema::table('companies', function (Blueprint $table) {
            $table->boolean('manual_contract_approval')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}

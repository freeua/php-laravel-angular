<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnSupplierBlind extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('portal_supplier', function (Blueprint $table) {
            $table->float('supplier_blind', 8, 2)->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('portal_supplier', function (Blueprint $table) {
            $table->dropColumn('supplier_blind');
        });
    }
}

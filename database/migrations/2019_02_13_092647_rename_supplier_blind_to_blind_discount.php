<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameSupplierBlindToBlindDiscount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('portal_supplier', function (Blueprint $table) {
            $table->renameColumn('supplier_blind', 'blind_discount');
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
            $table->renameColumn('blind_discount', 'supplier_blind');
        });
    }
}

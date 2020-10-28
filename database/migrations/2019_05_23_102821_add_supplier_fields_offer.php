<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSupplierFieldsOffer extends Migration
{
    public function up()
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->string('supplier_country')->nullable()->after('supplier_city');
            $table->string('supplier_bank_account')->nullable()->after('supplier_city');
            $table->string('supplier_bank_name')->nullable()->after('supplier_city');
            $table->string('supplier_tax_id')->nullable()->after('supplier_city');
            $table->string('supplier_admin_name')->nullable()->after('supplier_city');
            $table->string('supplier_phone')->nullable()->after('supplier_city');
            $table->string('supplier_email')->nullable()->after('supplier_city');
        });
    }

    public function down()
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->dropColumn('supplier_country');
            $table->dropColumn('supplier_bank_account');
            $table->dropColumn('supplier_bank_name');
            $table->dropColumn('supplier_tax_id');
            $table->dropColumn('supplier_admin_name');
            $table->dropColumn('supplier_phone');
            $table->dropColumn('supplier_email');
        });
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSupplierFieldsOrderContract extends Migration
{
    public function up()
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->string('supplier_gp_number')->nullable()->after('supplier_city');
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->string('supplier_country')->nullable()->after('supplier_city');
            $table->string('supplier_bank_account')->nullable()->after('supplier_city');
            $table->string('supplier_bank_name')->nullable()->after('supplier_city');
            $table->string('supplier_tax_id')->nullable()->after('supplier_city');
            $table->string('supplier_admin_name')->nullable()->after('supplier_city');
            $table->string('supplier_phone')->nullable()->after('supplier_city');
            $table->string('supplier_email')->nullable()->after('supplier_city');
            $table->string('supplier_gp_number')->nullable()->after('supplier_city');
        });

        Schema::table('contracts', function (Blueprint $table) {
            $table->string('supplier_country')->nullable()->after('supplier_city');
            $table->string('supplier_bank_account')->nullable()->after('supplier_city');
            $table->string('supplier_bank_name')->nullable()->after('supplier_city');
            $table->string('supplier_tax_id')->nullable()->after('supplier_city');
            $table->string('supplier_admin_name')->nullable()->after('supplier_city');
            $table->string('supplier_phone')->nullable()->after('supplier_city');
            $table->string('supplier_email')->nullable()->after('supplier_city');
            $table->string('supplier_gp_number')->nullable()->after('supplier_city');
        });
        \App\Portal\Models\Order::query()->get()->each(function(\App\Portal\Models\Order $order) {
            $offer = $order->offer;
            $order->supplierCountry = $offer->supplierCountry;
            $order->supplierBankName = $offer->supplierBankName;
            $order->supplierBankAccount = $offer->supplierBankAccount;
            $order->supplierTaxId = $offer->supplierTaxId;
            $order->supplierEmail = $offer->supplierEmail;
            $order->supplierPhone = $offer->supplierPhone;
        });
        \App\Portal\Models\Contract::query()->get()->each(function(\App\Portal\Models\Contract $contract) {
            $order = $contract->order;
            $contract->supplierCountry = $order->supplierCountry;
            $contract->supplierBankName = $order->supplierBankName;
            $contract->supplierBankAccount = $order->supplierBankAccount;
            $contract->supplierTaxId = $order->supplierTaxId;
            $contract->supplierEmail = $order->supplierEmail;
            $contract->supplierPhone = $order->supplierPhone;
        });
    }


    public function down()
    {
        //
    }
}

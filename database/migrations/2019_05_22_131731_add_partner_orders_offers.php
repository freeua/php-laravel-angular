<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPartnerOrdersOffers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('offers', function(Blueprint $table) {
            $table->unsignedInteger('partner_id')->nullable();
            $table->unsignedInteger('supplier_id')->nullable()->change();
            $table->foreign('partner_id')->references('id')->on('partners');
        });
        Schema::table('orders', function(Blueprint $table) {
            $table->unsignedInteger('partner_id')->nullable();
            $table->unsignedInteger('supplier_id')->nullable()->change();
            $table->foreign('partner_id')->references('id')->on('partners');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('offers', function(Blueprint $table) {
            $table->dropForeign('offers_partner_id_foreign');
            $table->dropIndex('offers_partner_id_foreign');
            $table->dropColumn('partner_id');
            $table->unsignedInteger('supplier_id')->nullable(false)->change();
        });
        Schema::table('orders', function(Blueprint $table) {
            $table->dropForeign('orders_partner_id_foreign');
            $table->dropIndex('orders_partner_id_foreign');
            $table->dropColumn('partner_id');
            $table->unsignedInteger('supplier_id')->nullable(false)->change();
        });
    }
}

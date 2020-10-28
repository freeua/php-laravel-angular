<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderDocumentsFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function(Blueprint $table) {
            $table->string('single_leasing_file')->nullable();
            $table->string('takeover_file')->nullable();
            $table->string('invoice_file')->nullable();
            $table->string('supplier_offer_file')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function(Blueprint $table) {
            $table->dropColumn('single_leasing_file');
            $table->dropColumn('takeover_file');
            $table->dropColumn('invoice_file');
            $table->dropColumn('supplier_offer_file');
        });
    }
}

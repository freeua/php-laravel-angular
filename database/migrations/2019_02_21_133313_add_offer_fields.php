<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOfferFields extends Migration
{
    public function up()
    {
        \Schema::table('offers', function (Blueprint $table) {
            $table->unsignedInteger('supplier_id');
            $table->foreign('supplier_id')->on('suppliers')->references('id');
            $table->renameColumn('normal_price', 'bike_list_price');
            $table->renameColumn('discount_price', 'bike_discounted_price');
            $table->float('bike_discount');
            $table->float('accessories_discounted_price');
        });
    }

    public function down()
    {
        \Schema::table('offers', function (Blueprint $table) {
            $table->renameColumn('bike_list_price', 'normal_price');
            $table->renameColumn('bike_discounted_price', 'discount_price');
            $table->dropColumn('bike_discount');
            $table->dropColumn('accessories_discounted_price');
        });
    }
}

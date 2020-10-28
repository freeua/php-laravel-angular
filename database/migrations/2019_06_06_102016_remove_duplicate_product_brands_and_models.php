<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveDuplicateProductBrandsAndModels extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('products')->whereNotNull('supplier_id')->delete();
        DB::table('product_models')->whereNotNull('supplier_id')->delete();
        DB::table('product_brands')->whereNotNull('supplier_id')->delete();
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

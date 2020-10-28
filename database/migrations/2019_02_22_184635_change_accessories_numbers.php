<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeAccessoriesNumbers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Schema::table('offer_accessories', function(Blueprint $table) {
            $table->float('amount')->change();
            $table->float('price')->change();
            $table->float('discount')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \Schema::table('offer_accessories', function(Blueprint $table) {
            $table->decimal('amount')->change();
            $table->decimal('price')->change();
            $table->decimal('discount')->change();
        });
    }
}

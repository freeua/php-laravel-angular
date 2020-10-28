<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCardIssueDateToOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Schema::table('orders', function (Blueprint $table) {
            $table->removeColumn('card_issue_city');
            $table->date('card_issue_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \Schema::table('orders', function (Blueprint $table) {
            $table->removeColumn('card_issue_date');
            $table->string('card_issue_city')->nullable();
        });
    }
}

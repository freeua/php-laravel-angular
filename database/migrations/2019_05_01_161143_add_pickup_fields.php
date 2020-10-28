<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPickupFields extends Migration
{
    public function up()
    {
        \Schema::table('orders', function (Blueprint $table) {
            $table->string('card_issue_city')->nullable();
            $table->string('card_issue_authority')->nullable();
            $table->string('frame_number')->nullable();
        });
    }

    public function down()
    {
        \Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('card_issue_city');
            $table->dropColumn('card_issue_authority');
            $table->dropColumn('frame_number');
        });
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSenderRelation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->unsignedInteger('sender_id')->nullable();
            $table->renameColumn('sender', 'sender_name');
            $table->foreign('sender_id')->on('portal_users')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->dropForeign('offers_sender_id_foreign');
            $table->dropColumn('sender_id');
        });
    }
}

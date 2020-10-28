<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAutoresponderTextPortal extends Migration
{
    public function up()
    {
        Schema::table('portals', function (Blueprint $table) {
            $table->text('autoresponder_text')->nullable(true);
        });
    }

    public function down()
    {
        Schema::table('portals', function (Blueprint $table) {
            $table->dropColumn('autoresponder_text');
        });
    }
}

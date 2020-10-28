<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCreditNoteReadField extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('credit_note_read')->default(false);
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('credit_note_read');
        });
    }
}

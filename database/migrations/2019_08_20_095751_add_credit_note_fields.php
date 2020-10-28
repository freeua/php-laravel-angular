<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCreditNoteFields extends Migration
{
    public function up()
    {
        \Schema::table('orders', function (Blueprint $table) {
            $table->text('credit_note_file')->nullable();
        });
        \Schema::table('portals', function (Blueprint $table) {
            $table->boolean('automatic_credit_note');
        });
    }

    public function down()
    {
    }
}

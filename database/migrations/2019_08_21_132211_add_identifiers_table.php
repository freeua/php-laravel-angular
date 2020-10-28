<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIdentifiersTable extends Migration
{
    public function up()
    {
        Schema::create('identifiers', function (Blueprint $table) {
            $table->unsignedInteger('id')->autoIncrement();
            $table->string('type');
            $table->string('format');
            $table->string('last_value');
            $table->unsignedInteger('next_number');
            $table->dateTime('next_reset')->nullable();
            $table->unsignedInteger('year_identifier')->nullable();
            $table->timestamps();
        });
        DB::table('identifiers')->insert([
            'type' => 'leasing_credit_note',
            'format' => 'L-%\'06d-%d',
            'last_value' => '',
            'next_number' => 1,
            'next_reset' => \Carbon\Carbon::create(2020,7),
            'year_identifier' => 29,
        ]);
        DB::table('identifiers')->insert([
            'type' => 'inspection_credit_note',
            'format' => 'WL-1%\'06d-%d',
            'last_value' => '',
            'next_number' => 1,
            'next_reset' => \Carbon\Carbon::create(2020,7),
            'year_identifier' => 29,
        ]);
    }

    public function down()
    {
        Schema::drop('identifiers');
    }
}

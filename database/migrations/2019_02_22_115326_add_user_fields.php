<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Schema::table('portal_users', function(Blueprint $table) {
            $table->string('salutation')->nullable();
            $table->string('street')->nullable();
            $table->unsignedInteger('city_id')->nullable();
            $table->foreign('city_id')->on('cities')->references('id');
            $table->string('postal_code')->nullable();
            $table->string('country')->nullable();
            $table->string('employee_number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {\Schema::table('portal_users', function(Blueprint $table) {
        $table->dropColumn('salutation');
        $table->dropColumn('street');
        $table->dropColumn('city_id');
        $table->dropColumn('postal_code');
        $table->dropColumn('country');
        $table->dropColumn('employee_number');
    });
    }
}

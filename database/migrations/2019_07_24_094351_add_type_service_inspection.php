<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTypeServiceInspection extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Schema::table('service_rates', function(Blueprint $table) {
            $table->string('type');
        });
        \DB::table('service_rates')->update(['type' => 'inspection']);
    }

    public function down()
    {
        \Schema::table('service_rates', function(Blueprint $table) {
            $table->dropColumn('type');
        });
    }
}

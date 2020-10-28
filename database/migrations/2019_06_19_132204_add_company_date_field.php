<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCompanyDateField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function(Blueprint $table) {
            $table->dateTime('end_contract')->nullable(true)->useCurrent();

        });

        \DB::table('companies')->update(['end_contract' => \Carbon\Carbon::now()]);
        Schema::table('companies', function(Blueprint $table) {
            $table->dateTime('end_contract')->nullable(false)->useCurrent()->change();

        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}

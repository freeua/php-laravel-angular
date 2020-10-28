<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMinUserAmountField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Schema::table('companies', function (Blueprint $table) {
            $table->double('min_user_amount')->default(749);
        });
        \Schema::table('portal_users', function (Blueprint $table) {
            $table->double('min_user_amount')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('min_user_amount');
        });
        \Schema::table('portal_users', function (Blueprint $table) {
            $table->dropColumn('min_user_amount');
        });
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangePecuniaryAdvantageField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->renameColumn('hide_pecuniary_advantage', 'pecuniary_advantage')->default(true);
        });

        \DB::table('companies')->update([
            'pecuniary_advantage' => true,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->renameColumn('pecuniary_advantage', 'hide_pecuniary_advantage')->default(false);
        });

        \DB::table('companies')->update([
            'hide_pecuniary_advantage' => false,
        ]);
    }
}

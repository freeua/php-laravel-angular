<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeLabelToStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('statuses')->where('label', 'Angebot Erhalten')->update([
            'label' => 'Angebot erhalten'
        ]);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('statuses')->where('label', 'Angebot erhalten')->update([
            'label' => 'Angebot Erhalten'
        ]);
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class StatusIcons extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('statuses')->where('id', 9)->update(
            [
                'icon' => 'block',
            ]);
        DB::table('statuses')->where('id', 10)->update(
            [
                'icon' => 'done',
            ]);
        DB::table('statuses')->where('id', 11)->update(
            [
                'icon' => 'hourglass_empty',
            ]);
    }
}

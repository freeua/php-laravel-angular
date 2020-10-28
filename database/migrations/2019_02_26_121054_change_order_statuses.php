<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeOrderStatuses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::query()->from('statuses')->where(['id' => 12])->update(['type' => 'success']);
        \DB::query()->from('statuses')->where(['id' => 13])->update(['type' => 'info']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::query()->from('statuses')->where(['id' => 12])->update(['type' => 'info']);
        \DB::query()->from('statuses')->where(['id' => 13])->update(['type' => 'success']);
    }
}

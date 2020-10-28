<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeStatusNames extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::table('statuses')->where('id', 11)->update([
            'label' => 'Erhalten'
        ]);
        \DB::table('statuses')->where('id', 18)->update([
            'label' => 'Ausstehend'
        ]);
        \DB::table('statuses')->where('id', 20)->update([
            'label' => 'UV akzeptiert'
        ]);
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

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDraftOfferToStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('statutes', function (Blueprint $table) {
            DB::table('statuses')->insert([
                'id' => 19,
                'label' => 'Entwurf',
                'type' => 'default',
                'table' => 'offers',
            ]
            );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('statutes', function (Blueprint $table) {
            DB::table('statuses')->where('id', 19)->delete();
        });
    }
}

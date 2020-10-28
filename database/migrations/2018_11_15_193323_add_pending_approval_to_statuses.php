<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class AddPendingApprovalToStatuses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('statuses')->insert([
            [
                'label' => 'Ausstehende Genehmigung',
                'type' => 'default',
                'table' => 'offers',
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('statuses')->where('id', 18)->delete();
    }
}

<?php

use Illuminate\Database\Migrations\Migration;

class AddCancelledContractStatusServices extends Migration
{

    public function up()
    {
        DB::table('statuses')->insert([
                'id' => 27,
                'label' => 'Vertrag wurde storniert',
                'type' => 'danger',
                'icon' => 'close',
                'table' => 'technical_services'
            ]
        );
        DB::table('statuses')->insert([
                'id' => 28,
                'label' => 'Storniert',
                'type' => 'danger',
                'icon' => 'close',
                'table' => 'technical_services'
            ]
        );
    }

    public function down()
    {
        //
    }
}

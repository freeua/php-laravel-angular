<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeStatusTechnicalService extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('statuses')->where('id', 23)->update([
                'label' => 'in Bearbeitung',
                'type' => 'info',
                'icon' => 'build',
                'table' => 'technical_services'
            ]
        );
        DB::table('statuses')->where('id', 24)->update([
                'label' => 'Abgeschlossen',
                'type' => 'success',
                'icon' => 'check_circle',
                'table' => 'technical_services'
            ]
        );
        DB::table('statuses')->where('id', 25)->update([
                'label' => 'Offen',
                'type' => 'warning',
                'icon' => 'access_time',
                'table' => 'technical_services'
            ]
        );
        DB::table('statuses')->where('id', 26)->update([
                'label' => 'Abholbereit',
                'type' => 'success',
                'icon' => 'thumb_up',
                'table' => 'technical_services'
            ]
        );
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

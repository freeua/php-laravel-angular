<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('label');
            $table->string('icon')->nullable();
            $table->string('type')->default('default');
            $table->string('table');
            $table->timestamps();
        });

        DB::table('statuses')->insert([
            [
                'id' => 1,
                'label' => 'Aktiv',
                'type' => 'success',
                'table' => 'users',
            ],
            [
                'id' => 2,
                'label' => 'Inaktiv',
                'type' => 'default',
                'table' => 'users',
            ],
            [
                'id' => 3,
                'label' => 'Aktiv',
                'type' => 'success',
                'table' => 'portals',
            ],
            [
                'id' => 4,
                'label' => 'Inaktiv',
                'type' => 'default',
                'table' => 'portals',
            ],
            [
                'id' => 5,
                'label' => 'Aktiv',
                'type' => 'success',
                'table' => 'companies',
            ],
            [
                'id' => 6,
                'label' => 'Inaktiv',
                'type' => 'default',
                'table' => 'companies',
            ],
            [
                'id' => 7,
                'label' => 'Aktiv',
                'type' => 'success',
                'table' => 'suppliers',
            ],
            [
                'id' => 8,
                'label' => 'Inaktiv',
                'type' => 'default',
                'table' => 'suppliers',
            ],
            [
                'id' => 9,
                'label' => 'Abgelehnt',
                'type' => 'danger',
                'table' => 'offers',
            ],
            [
                'id' => 10,
                'label' => 'Akzeptiert',
                'type' => 'success',
                'table' => 'offers',
            ],
            [
                'id' => 11,
                'label' => 'Angebot Erhalten',
                'type' => 'warning',
                'table' => 'offers',
            ],
            [
                'id' => 12,
                'label' => 'Abholbereit',
                'type' => 'progress',
                'table' => 'orders',
            ],
            [
                'id' => 13,
                'label' => 'Abgeschlossen',
                'type' => 'success',
                'table' => 'orders',
            ],
            [
                'id' => 14,
                'label' => 'Offen',
                'type' => 'warning',
                'table' => 'orders',
            ],
            [
                'id' => 15,
                'label' => 'Aktiv',
                'type' => 'success',
                'table' => 'contracts',
            ],
            [
                'id' => 16,
                'label' => 'Inaktiv',
                'type' => 'default',
                'table' => 'contracts',
            ],
            [
                'id' => 17,
                'label' => 'Offen',
                'type' => 'default',
                'table' => 'users',
            ],
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('statuses');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewStatusesForOrderAndContractToStatusesTable extends Migration
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
                    'id' => 21,
                    'label' => 'Storniert',
                    'type' => 'danger',
                    'icon' => 'close',
                    'table' => 'contracts'
                ]
            );
            DB::table('statuses')->insert([
                    'id' => 22,
                    'label' => 'Vertrag wurde storniert',
                    'type' => 'danger',
                    'icon' => 'close',
                    'table' => 'orders'
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
            DB::statement('SET FOREIGN_KEY_CHECKS = 0');
            DB::table('statuses')->where('id', 21)->delete();
            DB::table('statuses')->where('id', 22)->delete();
            DB::statement('SET FOREIGN_KEY_CHECKS = 1');
        });
    }
}

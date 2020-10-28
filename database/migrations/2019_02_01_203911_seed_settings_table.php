<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SeedSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $settings = array(
            array('key' => 'domain', 'value' => config('app.system_admin_domain')),
            array('key' => 'timezone', 'value' => config('app.timezone')),
            array('key' => 'color', 'value' => '#ec4640'),
            array('key' => 'logo', 'value' => ''),
            array('key' => 'email', 'value' => ''));

        DB::table('settings')->insert($settings);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('settings')->where('key', 'domain')->delete();
        DB::table('settings')->where('key', 'color')->delete();
        DB::table('settings')->where('key', 'timezone')->delete();
        DB::table('settings')->where('key', 'logo')->delete();
        DB::table('settings')->where('key', 'email')->delete();
    }
}

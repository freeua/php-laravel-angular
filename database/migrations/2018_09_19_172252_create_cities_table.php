<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateCitiesTable
 */
class CreateCitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('lat');
            $table->string('lng');
        });
        $file = resource_path('data/cities.json');

        if (!file_exists($file)) {
            throw new Error('Cities json file not found');
        }

        $data = json_decode(file_get_contents($file));

        foreach ($data as $item) {
            DB::table('cities')->insert([
                'name' => $item->city,
                'lat'  => $item->lat,
                'lng'  => $item->lng
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cities');
    }
}

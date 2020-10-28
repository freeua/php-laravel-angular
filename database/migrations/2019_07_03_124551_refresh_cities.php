<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RefreshCities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cities', function(Blueprint $table) {
            if (Schema::hasColumn('cities', 'lat')) {
                $table->dropColumn('lat');
            }
            if (Schema::hasColumn('cities', 'lng')) {
                $table->dropColumn('lng');
            }
        });
        $file = resource_path('data/new-cities.json');

        if (!file_exists($file)) {
            throw new Error('Cities json file not found');
        }

        $data = json_decode(file_get_contents($file));
        $cities = \App\Models\City::all(['id', 'name'])->keyBy('name');
        $citiesToInsert = [];
        foreach ($data as $item) {
            $city = $cities->get($item->city);
            if (!$city) {
                array_push($citiesToInsert, ['name' => $item->city]);
            }
        }
        DB::table('cities')->insert($citiesToInsert);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('cities');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
        Schema::create('cities', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });
        $file = resource_path('data/cities-complete.json');

        if (!file_exists($file)) {
            throw new Error('Cities json file not found');
        }

        $data = json_decode(file_get_contents($file));
        $id = 1;
        foreach ($data as $item) {
            DB::table('cities')->updateOrInsert([
                'id' => $id,
            ], [
                'id' => $id,
                'name' => $item->city,
            ]);
            $id++;
        }
    }
}

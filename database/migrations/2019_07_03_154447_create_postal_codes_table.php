<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostalCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('postal_codes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('city_id')->unsigned();
            $table->foreign('city_id')->references('id')->on('cities');
            $table->string('code');
        });

        $file = resource_path('data/postal_code.json');

        if (!file_exists($file)) {
            throw new Error('Postal Code json file not found');
        }

        $data = json_decode(file_get_contents($file));
        $cities = \App\Models\City::all(['id', 'name'])->keyBy('name');
        $postalCodes = [];
        foreach ($data as $item) {
            $city = $cities->get($item->city);
            if (!$city) {
                throw new Error($item->city . " is missing on cities table!!!");
            }
            array_push($postalCodes, [
                'city_id' => $city->id,
                'code' => $item->post,
            ]);
        }

        DB::table('postal_codes')->insert($postalCodes);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('postal_codes');
    }
}

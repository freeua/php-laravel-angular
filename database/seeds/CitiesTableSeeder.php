<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Class CitiesTableSeeder
 */
class CitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $file = resource_path('data/cities.json');

        if (!file_exists($file)) {
            $this->command->error('Cities json file not found');

            return;
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
}

<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Class ProductTypesTableSeeder
 */
class ProductTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('product_types')->insert([
            'name' => 'Bike',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
        DB::table('product_types')->insert([
            'name' => 'Product type 1',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
        DB::table('product_types')->insert([
            'name' => 'Product type 2',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}

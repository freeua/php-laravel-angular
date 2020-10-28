<?php

use Illuminate\Database\Seeder;
use App\System\Models\ReportCategory;

/**
 * Class ReportCategoriesSeeder
 */
class ReportCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 1; $i <= 20; $i++) {
            ReportCategory::create([
                'name' => 'Report Category ' . $i,
            ]);
        }
    }
}

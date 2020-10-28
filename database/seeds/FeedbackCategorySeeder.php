<?php

use Illuminate\Database\Seeder;
use App\System\Models\FeedbackCategory;

/**
 * Class FeedbackCategorySeeder
 */
class FeedbackCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            ['name' => 'Funktionserweiterung'],
        ];

        foreach ($categories as $row) {
            FeedbackCategory::firstOrCreate([
                'name' => $row['name'],
            ]);
        }
    }
}

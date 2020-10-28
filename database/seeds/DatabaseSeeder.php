<?php

use Illuminate\Database\Seeder;

/**
 * Class DatabaseSeeder
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UsersTableSeeder::class,
            PortalSeeder::class,
            ProductCategories::class,
            FeedbackCategorySeeder::class,
            ReportCategoriesSeeder::class,
            CompanySeeder::class,
            SupplierSeeder::class,
            ProductBrandsSeeder::class,
            ProductModelsSeeder::class,
            ProductSeeder::class,
            PortalLeasingConditionSeeder::class,
            CompanyLeasingSettingsSeeder::class,
            PartnerSeeder::class,
        ]);
    }
}

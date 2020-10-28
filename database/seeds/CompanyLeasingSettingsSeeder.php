<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CompanyLeasingSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $companies = \App\Models\Companies\Company::query()->get(['id']);
        $productCategories = \App\Models\ProductCategory::query()->get(['id']);
        foreach ($companies as $company) {
            foreach ($productCategories as $productCategory) {
                factory(\App\Models\LeasingCondition::class)->create([
                    'company_id' => $company->id,
                    'product_category_id' => $productCategory->id,
                    'activeAt' => Carbon::now(new \DateTimeZone('Europe/Berlin')),
                ]);
                factory(\App\Models\Rates\InsuranceRate::class)->create([
                    'name' => 'Standard rate',
                    'company_id' => $company->id,
                    'product_category_id' => $productCategory->id,
                    'default' => true,
                    'activeAt' => Carbon::now(new \DateTimeZone('Europe/Berlin')),
                ]);
                factory(\App\Models\Rates\ServiceRate::class)->create([
                    'name' => 'Standard rate',
                    'company_id' => $company->id,
                    'product_category_id' => $productCategory->id,
                    'default' => true,
                    'activeAt' => Carbon::now(new \DateTimeZone('Europe/Berlin')),
                ]);
                factory(\App\Models\Rates\InsuranceRate::class)->create([
                    'name' => 'Premium rate',
                    'company_id' => $company->id,
                    'product_category_id' => $productCategory->id,
                    'default' => true,
                    'activeAt' => Carbon::now(new \DateTimeZone('Europe/Berlin')),
                ]);
                factory(\App\Models\Rates\ServiceRate::class)->create([
                    'name' => 'Premium rate',
                    'company_id' => $company->id,
                    'product_category_id' => $productCategory->id,
                    'activeAt' => Carbon::now(new \DateTimeZone('Europe/Berlin')),
                ]);
            }
        }
    }
}

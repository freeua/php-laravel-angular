<?php

use Illuminate\Database\Seeder;

class PortalLeasingConditionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $portals = \App\Models\Portal::query()->get(['id']);
        $productCategories = \App\Models\ProductCategory::query()->get(['id']);
        foreach ($portals as $portal) {
            foreach ($productCategories as $productCategory) {
                factory(\App\Models\LeasingCondition::class)->create([
                    'portal_id' => $portal->id,
                    'product_category_id' => $productCategory->id,
                    'default' => true,
                ]);
                factory(\App\Models\LeasingCondition::class)->create([
                    'portal_id' => $portal->id,
                    'product_category_id' => $productCategory->id,
                ]);
                factory(\App\Models\Rates\InsuranceRate::class)->create([
                    'portal_id' => $portal->id,
                    'product_category_id' => $productCategory->id,
                    'default' => true,
                ]);
                factory(\App\Models\Rates\InsuranceRate::class)->create([
                    'portal_id' => $portal->id,
                    'product_category_id' => $productCategory->id,
                ]);
                factory(\App\Models\Rates\ServiceRate::class)->create([
                    'portal_id' => $portal->id,
                    'product_category_id' => $productCategory->id,
                    'default' => true,
                ]);
                factory(\App\Models\Rates\ServiceRate::class)->create([
                    'portal_id' => $portal->id,
                    'product_category_id' => $productCategory->id,
                ]);
            }
        }
    }
}

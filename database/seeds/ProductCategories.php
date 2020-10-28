<?php

use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductCategories extends Seeder
{
    public function run()
    {
        $fahrrad = new ProductCategory([
            'name' => 'Fahrrad',
            'leasingConditions' => [factory(\App\Models\LeasingCondition::class)->make()],
            'insuranceRates' => [factory(\App\Models\Rates\InsuranceRate::class)->make()],
            'serviceRates' => [factory(\App\Models\Rates\ServiceRate::class)->make()],
        ]);
        $fahrrad->save();
        $pedelec = new ProductCategory([
            'name' => 'Pedelec',
            'leasingConditions' => [factory(\App\Models\LeasingCondition::class)->make()],
            'insuranceRates' => [factory(\App\Models\Rates\InsuranceRate::class)->make()],
            'serviceRates' => [factory(\App\Models\Rates\ServiceRate::class)->make()],
        ]);
        $pedelec->save();
    }
}

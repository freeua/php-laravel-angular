<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Class ProductBrandsTableSeeder
 */
class ProductBrandsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Usage: php artisan tenancy:db:seed --class=ProductBrandsTableSeeder
     *
     * @return void
     */
    public function run()
    {
        $supplier = \App\Portal\Models\Supplier::query()->find(1);
        factory(\App\Portal\Models\ProductBrand::class, 6)->make()
            ->each(function($brand) use ($supplier) {
                $brand->supplier_id = $supplier->id;
                $brand->save();
            });
    }
}

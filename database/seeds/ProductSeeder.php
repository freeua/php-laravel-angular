<?php

use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $suppliers = app(\App\Portal\Models\Supplier::class)->newQuery()
            ->join('product_brands', 'suppliers.id', '=', 'product_brands.supplier_id')
            ->groupBy(['suppliers.id'])
            ->get(['suppliers.id']);
        $categories = app(\App\Models\ProductCategory::class)->newQuery()->get(['id']);
        foreach ($suppliers as $supplier) {
            $brands = app(\App\Portal\Models\ProductBrand::class)->newQuery()
                ->where('supplier_id', '=', $supplier->id)->get(['id']);
            $models = app(\App\Portal\Models\ProductModel::class)->newQuery()
                ->where('supplier_id', '=', $supplier->id)->get(['id']);
            factory(\App\Portal\Models\Product::class, 2)->make()
                ->each(function($product) use ($suppliers, $brands, $categories, $models) {
                    $product->supplier_id = $suppliers->random()->id;
                    $product->brand_id = $brands->random()->id;
                    $product->category_id = $categories->random()->id;
                    $product->model_id = $models->random()->id;
                    $product->save();
                });
        }

    }
}

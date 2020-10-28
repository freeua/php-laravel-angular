<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

/**
 * Class ProductModelsTableSeeder
 */
class ProductModelsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Usage: php artisan tenancy:db:seed --class=ProductModelsTableSeeder
     *
     * @return void
     */
    public function run()
    {
        $models = [
            'Carrera Vengeance',
            'Carrera Sulcata',
            'Apollo Phaze',
            'Carrera Vulcan',
            'Carrera Valour',
            'Boardman',
            'Voodoo Bantu',
            'Ridge',
            'Apollo Slant',
            'Voodoo Hoodoo',
            'Voodoo Bizango',
            'Carrera Kraken',
            'Boardman MTR',
            'Voodoo Canzo Full Suspension',
            'Boardman MTR 8.6',
            'Voodoo Aizan',
            'Ridge Full Suspension',
            'Boardman MHT 8.6',
            'Apollo Entice',
            'Apollo Radar',
            'Apollo Gradient',
            'Voodoo Nzumbi',
            'Boardman MHT 8.9',
            'Apollo Twilight',
        ];

        $suppliers = app(\App\Portal\Models\Supplier::class)->newQuery()
            ->join('product_brands', 'suppliers.id', '=', 'product_brands.supplier_id')
            ->groupBy(['suppliers.id'])
            ->get(['suppliers.id']);
        $categories = app(\App\Models\ProductCategory::class)->newQuery()->get(['id']);
        foreach ($suppliers as $supplier) {
            $brands = app(\App\Portal\Models\ProductBrand::class)->newQuery()
                ->where('supplier_id', '=', $supplier->id)->get(['id']);
            foreach ($categories as $category) {
                foreach ($brands as $brand) {
                    foreach ($models as $model) {
                        factory(\App\Portal\Models\ProductModel::class)->create([
                            'name' => $model,
                            'supplier_id' => $supplier->id,
                            'brand_id' => $brand,
                            'category_id' => $category,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ]);
                    }
                }
            }
        }

    }
}

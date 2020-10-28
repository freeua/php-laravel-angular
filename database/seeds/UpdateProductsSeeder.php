<?php

use App\Portal\Models\Offer;
use App\Portal\Models\Product;
use App\Portal\Models\ProductBrand;
use App\Portal\Models\ProductModel;
use App\Portal\Models\Supplier;
use App\Portal\Models\Contract;
use App\Portal\Models\Order;
use App\Models\Portal;
use Illuminate\Database\Seeder;

/**
 * Class UpdateProductsSeeder
 */
class UpdateProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Usage: php artisan tenancy:db:seed --class=UpdateProductsSeeder
     *
     * @return void
     */
    public function run()
    {
        $map = [
            'GIANT'       => [
                1 => [
                    'Propel Advanced SL Disc',
                    'Propel Advanced Pro',
                    'Defy Advanced',
                    'Contend SL',
                    'Omnium'
                ],
                2 => [
                    'Explore E + 2 STA',
                    'Explore E + 2 GTS',
                    'Tough Road E + GX'
                ]
            ],
            'SIMPLON'     => [
                1 => [
                    'Pride',
                    'Pavo Grandfondo Disc'
                ],
                2 => [
                    'SENGO 275',
                    'Steamer Compact'
                ]
            ],
            'BBF'         => [],
            'Checker Pig' => [
                1 => [
                    'Shimano Deore XT 11G',
                    'Shimano Altus 27-speed'
                ],
            ]
        ];

        $suppliers = Supplier::all();

        foreach ($map as $brandName => $item) {
            $brand = (new ProductBrand)->where('name', $brandName)
                ->first();

            if ($brand) {
                $brand->update(['name' => $brandName]);
            } else {
                $brand = ProductBrand::create([
                    'name' => $brandName
                ]);
            }

            if (!(new Product)->count()) {
                continue;
            }

            foreach ($item as $categoryId => $modelsNames) {
                foreach ($modelsNames as $modelName) {
                    $model = (new ProductModel)->where('name', $modelName)
                        ->first();

                    if ($model) {
                        $model->update(['name' => $modelName]);
                    } else {
                        $model = ProductModel::create([
                            'name' => $modelName
                        ]);
                    }

                    $latestProduct = (new Product)->latest()->first();
                    $code = intval($latestProduct->code);

                    foreach ($suppliers as $supplier) {
                        $exists = (new Product)
                            ->where('brand_id', $brand->id)
                            ->where('category_id', $categoryId)
                            ->where('model_id', $model->id)
                            ->where('supplier_id', $supplier->id)
                            ->first();

                        if ($exists) {
                            continue;
                        }

                        $product = Product::create([
                            'brand_id'    => $brand->id,
                            'category_id' => $categoryId,
                            'model_id'    => $model->id,
                            'supplier_id' => $supplier->id,
                            'code'        => str_pad(++$code, 12, '0', STR_PAD_LEFT)
                        ]);

                        if ($product) {
                            $product->size = 'M';
                            $product->color = 'White/Grey';
                            $product->save();
                        }
                    }
                }
            }
        }

        $portalDb = config('database.connections.tenant.database');
        $portal = (new Portal)->where('uuid', $portalDb)->first();

        if ((new Product)->count()) {
            foreach ($suppliers as $supplier) {
                $product = (new Product)
                    ->where('supplier_id', $supplier->id)
                    ->orderBy('id', 'desc')
                    ->first();

                (new Offer)->whereIn('supplier_user_id', $supplier->users->pluck('id'))
                    ->update(['product_id' => $product->id]);

                (new Order)->where('supplier_id', $supplier->system_source_id)
                    ->where('portal_id', $portal->id)
                    ->update(['product_id' => $product->id, 'product_name' => $product->model->name]);

                (new Contract)->where('supplier_id', $supplier->system_source_id)
                    ->where('portal_id', $portal->id)
                    ->update(['product_id' => $product->id, 'product_name' => $product->model->name]);
            }
        }

        $modelsNames = [
            'Propel Advanced SL Disc',
            'Propel Advanced Pro',
            'Defy Advanced',
            'Contend SL',
            'Omnium',
            'Explore E + 2 STA',
            'Explore E + 2 GTS',
            'Tough Road E + GX',
            'Pride',
            'Pavo Grandfondo Disc',
            'SENGO 275',
            'Steamer Compact',
            'Shimano Deore XT 11G',
            'Shimano Altus 27-speed'
        ];

        (new ProductBrand)->whereNotIn('name', array_keys($map))->delete();
        (new ProductModel)->whereNotIn('name', $modelsNames)->delete();

        $models = ProductModel::all();

        (new Product)->whereNotIn('model_id', $models->pluck('id'))->delete();
    }
}

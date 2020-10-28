<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;
use App\Portal\Models\ProductBrand;
use App\Portal\Models\ProductModel;

class InsertBicicliProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Schema::table('product_brands', function (Blueprint $table) {
            $table->unsignedInteger('supplier_id')->nullable()->change();
        });
        \Schema::table('product_models', function (Blueprint $table) {
            $table->unsignedInteger('supplier_id')->nullable()->change();
            $table->unsignedInteger('category_id')->nullable()->change();
        });
        $file = resource_path('data/bicicli-products.json');

        if (!file_exists($file)) {
            throw new Error('Bicicli products json (data/bicicli-products.json) file not found');
        }

        $data = json_decode(file_get_contents($file)); 

        $brands = collect($data)->groupBy("Marke");
        foreach ($brands as $brand => $models) {
            $productBrand = ProductBrand::create(['name' => $brand]);
            foreach($models as $model) {
                ProductModel::create(['name' => $model->Modell, 'brand_id' => $productBrand->id]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}

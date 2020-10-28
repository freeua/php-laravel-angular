<?php

use App\Portal\Models\Product;
use App\Portal\Models\ProductSize;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Portal\Models\ProductColor;

class CreateColorAndSizeTables extends Migration
{

    public function up()
    {
        Schema::create('product_colors', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->unsignedInteger('supplier_id')->nullable();
            $table->foreign('supplier_id')->on('suppliers')->references('id');
            $table->timestamps();
        });

        Schema::create('product_sizes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->unsignedInteger('supplier_id')->nullable();
            $table->foreign('supplier_id')->on('suppliers')->references('id');
            $table->timestamps();
        });

        $white = ProductColor::create([
            'name' => 'White',
        ]);

        $XL = ProductSize::create([
            'name' => 'XL',
        ]);

        $colors = [
            'Teal',
            'Black',
            'White',
            'Black/Grey',
            'Dark Red',
            'Matt Olive',
            'Red/Black',
            'White/Grey',
            'Black/Blue',
            'Berry',
        ];

        foreach ($colors as $color) {
            DB::table('product_colors')->insert([
                'name'        => $color,
            ]);
        }


        $sizes = [
            'XXS',
            'XS',
            'S',
            'M',
            'L',
            'XL',
            'XXL',
            'XXXL'
        ];

        foreach ($sizes as $size) {
            DB::table('product_sizes')->insert([
                'name'        => $size,
            ]);
        }

        Schema::table('products', function (Blueprint $table) {
            $table->string('size');
            $table->string('color');
        });
        Product::query()->update([
            'size' => 'XL',
            'color' => 'White'
        ]);
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign('products_size_id_foreign');
            $table->dropForeign('products_color_id_foreign');
        });
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('size_id');
            $table->dropColumn('color_id');
        });
        Schema::dropIfExists('product_sizes');
        Schema::dropIfExists('product_colors');
    }
}

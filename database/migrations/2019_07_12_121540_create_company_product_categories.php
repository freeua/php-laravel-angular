<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyProductCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_product_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->unsigned();
            $table->foreign('company_id')->references('id')->on('companies');
            $table->integer('category_id')->unsigned();
            $table->foreign('category_id')->references('id')->on('product_categories');
            $table->tinyInteger('status')->default(true);
            $table->timestamps();
        });

        \App\Models\ProductCategory::updateOrCreate(
            ['name' => 'S-Pedelec'],
            ['name' => 'S-Pedelec']
        );

        \App\Models\Companies\Company::all()->each(function (\App\Models\Companies\Company $company) {
            \App\Models\ProductCategory::all()->each(function (\App\Models\ProductCategory $productCategory) use ($company) {
                $companyProductCategory = new \App\Portal\Models\CompanyProductCategory([
                    'company_id' => $company->id,
                    'category_id' => $productCategory->id,
                    'status' => true
                ]);

                $companyProductCategory->saveOrFail();
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('company_product_categories');
    }
}

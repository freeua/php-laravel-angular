<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePortalRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Schema::create('insurance_rates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('amount_type');
            $table->float('amount');
            $table->float('minimum');
            $table->boolean('default')->default(false);
            $table->dateTime('active_at')->nullable();
            $table->dateTime('inactive_at')->nullable();
            $table->unsignedInteger('product_category_id');
            $table->foreign('product_category_id')->on('product_categories')->references('id');
            $table->unsignedInteger('company_id')->nullable();
            $table->foreign('company_id')->on('companies')->references('id');
            $table->unsignedInteger('portal_id')->nullable();
            $table->foreign('portal_id')->on('portals')->references('id');
            $table->timestamps();
            $table->softDeletes();
        });
        \Schema::create('service_rates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('amount_type');
            $table->float('amount');
            $table->float('minimum');
            $table->boolean('default')->default(false);
            $table->dateTime('active_at')->nullable();
            $table->dateTime('inactive_at')->nullable();
            $table->unsignedInteger('product_category_id');
            $table->foreign('product_category_id')->on('product_categories')->references('id');
            $table->unsignedInteger('company_id')->nullable();
            $table->foreign('company_id')->on('companies')->references('id');
            $table->unsignedInteger('portal_id')->nullable();
            $table->foreign('portal_id')->on('portals')->references('id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('insurance_rates');
        Schema::dropIfExists('service_rates');
    }
}

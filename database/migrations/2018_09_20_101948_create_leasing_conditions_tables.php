<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLeasingConditionsTables extends Migration
{
    public function up()
    {
        \Schema::create('leasing_conditions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_category_id')->unsigned();
            $table->foreign('product_category_id')->references('id')->on('product_categories');
            $table->integer('company_id')->unsigned()->nullable();
            $table->foreign('company_id')->references('id')->on('companies');
            $table->integer('portal_id')->unsigned()->nullable();
            $table->foreign('portal_id')->references('id')->on('portals');
            $table->float('factor');
            $table->integer('period');
            $table->float('residual_value')->nullable();
            $table->boolean('default')->default(0);
            $table->dateTime('active_at')->nullable();
            $table->dateTime('inactive_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        \Schema::drop('leasing_conditions');
        \Schema::drop('company_leasing_settings');
    }
}

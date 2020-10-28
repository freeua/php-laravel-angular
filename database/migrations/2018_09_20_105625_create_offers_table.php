<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('number', 9);
            $table->integer('portal_id')->unsigned();
            $table->foreign('portal_id')->references('id')->on('portals');
            $table->integer('company_id')->unsigned();
            $table->foreign('company_id')->references('id')->on('companies');
            $table->integer('product_id')->unsigned();
            $table->foreign('product_id')->references('id')->on('products');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('portal_users');
            $table->integer('supplier_user_id')->unsigned();
            $table->foreign('supplier_user_id')->references('id')->on('portal_users');
            $table->integer('service_rate_id')->unsigned()->nullable();
            $table->foreign('service_rate_id')->references('id')->on('service_rates');
            $table->integer('insurance_rate_id')->unsigned()->nullable();
            $table->foreign('insurance_rate_id')->references('id')->on('insurance_rates');
            $table->decimal('normal_price', 10,2);
            $table->decimal('discount_price', 10,2);
            $table->decimal('accessories_price', 10,2)->nullable();
            $table->date('expired_date')->nullable();
            $table->text('product_notes')->nullable();
            $table->text('notes')->nullable();
            $table->text('contract_data')->nullable();
            $table->string('contract_file')->nullable();
            $table->integer('status_id')->unsigned();
            $table->foreign('status_id')->references('id')->on('statuses');
            $table->timestamp('status_updated_at')->nullable();
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
        Schema::dropIfExists('offers');
    }
}

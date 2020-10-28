<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('number', 10)->nullable();
            $table->integer('portal_id')->unsigned();
            $table->foreign('portal_id')->references('id')->on('portals');
            $table->integer('order_id')->unsigned();
            $table->foreign('order_id')->references('id')->on('orders');
            $table->integer('supplier_id')->unsigned();
            $table->foreign('supplier_id')->references('id')->on('suppliers');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('portal_users');
            $table->integer('product_id')->unsigned();
            $table->foreign('product_id')->references('id')->on('products');
            $table->string('product_supplier')->nullable();
            $table->string('product_brand')->nullable();
            $table->string('product_category')->nullable();
            $table->string('product_name');
            $table->integer('company_id')->unsigned();
            $table->foreign('company_id')->references('id')->on('companies');
            $table->string('username')->index();
            $table->string('pickup_code')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('status_id')->unsigned()->nullable();
            $table->foreign('status_id')->references('id')->on('statuses');
            $table->double('agreed_purchase_price', 8,2);
            $table->double('list_price', 8,2);
            $table->double('leasing_rate', 8, 2);
            $table->double('insurance_rate', 8, 2);
            $table->double('service_rate', 8, 2);
            $table->double('leasing_rate_subsidy', 8, 2);
            $table->double('insurance_rate_subsidy', 8, 2);
            $table->double('service_rate_subsidy', 8, 2);
            $table->double('calculated_residual_value', 8, 2);
            $table->integer('leasing_period')->unsigned()->nullable();
            $table->string('product_size')->nullable();
            $table->text('product_notes')->nullable();
            $table->text('notes')->nullable();
            $table->dateTime('accepted_at')->nullable();
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
        Schema::dropIfExists('contracts');
    }
}

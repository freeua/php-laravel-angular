<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('number', 10)->unique();

            $table->integer('portal_id')->unsigned();
            $table->foreign('portal_id')->references('id')->on('portals');
            $table->integer('offer_id')->unsigned();
            $table->foreign('offer_id')->references('id')->on('offers');
            $table->integer('supplier_id')->unsigned();
            $table->foreign('supplier_id')->references('id')->on('suppliers');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('portal_users');
            $table->integer('product_id')->unsigned();
            $table->foreign('product_id')->references('id')->on('products');
            $table->integer('company_id')->unsigned();
            $table->foreign('company_id')->references('id')->on('companies');
            $table->string('username')->index();
            $table->string('company_name');
            $table->string('product_name');
            $table->string('pickup_code')->nullable();
            $table->integer('picked_up_by')->unsigned()->nullable();
            $table->foreign('picked_up_by')->references('id')->on('portal_users');
            $table->timestamp('picked_up_at')->nullable();
            $table->date('date');
            $table->string('zip', 20)->nullable();
            $table->integer('city_id')->unsigned()->nullable();
            $table->foreign('city_id')->references('id')->on('cities');
            $table->string('address')->nullable();
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
        Schema::dropIfExists('orders');
    }
}

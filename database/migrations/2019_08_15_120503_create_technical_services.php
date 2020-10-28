<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTechnicalServices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('technical_services', function (Blueprint $table) {
            $table->increments('id');
            $table->string('number', 11)->nullable(true);
            $table->string('number')->unique()->change();

            $table->integer('portal_id')->unsigned()->nullable();
            $table->foreign('portal_id')->references('id')->on('portals');

            $table->integer('company_id')->unsigned()->nullable();
            $table->foreign('company_id')->references('id')->on('companies');

            $table->integer('product_id')->unsigned()->nullable();
            $table->foreign('product_id')->references('id')->on('products');

            $table->integer('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('portal_users');

            $table->integer('order_id')->unsigned()->nullable();
            $table->foreign('order_id')->on('orders')->references('id');

            $table->integer('offer_id')->unsigned()->nullable();
            $table->foreign('offer_id')->on('offers')->references('id');

            $table->integer('contract_id')->unsigned()->nullable();
            $table->foreign('contract_id')->on('contracts')->references('id');

            $table->integer('product_category_id')->unsigned()->nullable();
            $table->foreign('product_category_id')->on('product_categories')->references('id');

            $table->string('product_size')->nullable();
            $table->string('product_color')->nullable();
            $table->string('product_model')->nullable();
            $table->string('product_brand')->nullable();
            $table->string('employee_city')->nullable();
            $table->string('employee_phone')->nullable();
            $table->string('employee_email')->nullable();
            $table->string('employee_number')->nullable();
            $table->string('employee_postal_code')->nullable();
            $table->string('employee_street')->nullable();
            $table->string('employee_name')->nullable();
            $table->string('employee_salutation')->nullable();
            $table->string('supplier_city')->nullable();
            $table->string('supplier_email')->nullable();
            $table->string('supplier_phone')->nullable();
            $table->string('supplier_admin_name')->nullable();
            $table->string('supplier_tax_id')->nullable();
            $table->string('supplier_bank_name')->nullable();
            $table->string('supplier_bank_account')->nullable();
            $table->string('supplier_country')->nullable();
            $table->string('supplier_postal_code')->nullable();
            $table->string('supplier_street')->nullable();
            $table->string('supplier_name')->nullable();
            $table->string('sender_name')->nullable();

            $table->unsignedInteger('supplier_id')->nullable();
            $table->foreign('supplier_id')->on('suppliers')->references('id');
            $table->string('inspection_code')->nullable();
            $table->date('delivery_date')->nullable();
            $table->unsignedInteger('partner_id')->nullable();
            $table->foreign('partner_id')->references('id')->on('partners');
            $table->string('employee_code', 11)->nullable();
            $table->string('supplier_code', 11)->nullable();
            $table->unsignedInteger('sender_id')->nullable();
            $table->string('sender')->nullable();

            $table->string('frame_number')->nullable();
            $table->string('service_modality')->nullable();
            $table->date('end_date')->nullable();

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
        Schema::dropIfExists('technical_services');
    }
}

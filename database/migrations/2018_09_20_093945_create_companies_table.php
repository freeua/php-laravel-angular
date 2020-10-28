<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 7)->nullable();
            $table->string('name');
            $table->string('slug');
            $table->string('logo')->nullable();
            $table->string('color')->nullable();
            $table->string('vat', 20);
            $table->string('invoice_type');
            $table->string('admin_first_name');
            $table->string('admin_last_name');
            $table->string('admin_email')->index();
            $table->string('zip', 20);
            $table->integer('city_id')->unsigned();
            $table->foreign('city_id')->references('id')->on('cities');
            $table->string('address');
            $table->string('phone')->nullable();
            $table->integer('max_user_contracts')->unsigned()->nullable();
            $table->double('max_user_amount', 10, 2)->nullable();
            $table->tinyInteger('insurance_covered')->default(0);
            $table->string('insurance_covered_type');
            $table->double('insurance_covered_amount', 10, 2)->nullable();
            $table->tinyInteger('maintenance_covered')->default(0);
            $table->string('maintenance_covered_type');
            $table->double('maintenance_covered_amount', 10, 2)->nullable();
            $table->tinyInteger('leasing_rate')->default(0);
            $table->string('leasing_rate_type');
            $table->double('leasing_rate_amount', 10, 2)->nullable();
            $table->integer('portal_id')->unsigned();
            $table->foreign('portal_id')->references('id')->on('portals');
            $table->integer('status_id')->unsigned();
            $table->foreign('status_id')->references('id')->on('statuses');
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
        Schema::dropIfExists('companies');
    }
}

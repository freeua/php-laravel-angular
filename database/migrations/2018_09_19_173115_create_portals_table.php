<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateTablePortals
 */
class CreatePortalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('portals', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('domain');
            $table->string('logo')->nullable();
            $table->string('color')->nullable();
            $table->string('application_key')->nullable();
            $table->string('admin_first_name');
            $table->string('admin_last_name');
            $table->string('admin_email');
            $table->string('company_name');
            $table->string('company_zip', 20)->nullable();
            $table->integer('company_city_id')->unsigned();
            $table->foreign('company_city_id')->references('id')->on('cities');
            $table->string('company_address');
            $table->string('company_vat', 30);
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
        Schema::dropIfExists('portals');
    }
}

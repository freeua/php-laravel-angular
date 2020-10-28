<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePortalUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('portal_users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code',10)->nullable();
            $table->integer('portal_id')->unsigned();
            $table->foreign('portal_id')->references('id')->on('portals');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->index();
            $table->string('avatar')->nullable();
            $table->string('password');
            $table->string('password_updated_at');
            $table->integer('status_id')->unsigned();
            $table->foreign('status_id')->references('id')->on('statuses');
            $table->integer('supplier_id')->unsigned()->nullable();
            $table->foreign('supplier_id')->references('id')->on('suppliers');
            $table->integer('company_id')->unsigned()->nullable();
            $table->foreign('company_id')->references('id')->on('companies');
            $table->integer('rejected_by')->unsigned()->nullable();
            $table->foreign('rejected_by')->references('id')->on('portal_users');
            $table->string('remember_token', 100)->nullable();
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
        Schema::dropIfExists('portal_users');
    }
}

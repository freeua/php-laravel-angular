<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAuditsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audits', function (Blueprint $table) {
            $table->increments('id');
            $table->string('description');
            $table->string('type');
            $table->integer('visibility');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')
                ->on('portal_users');
            $table->unsignedInteger('offer_id');
            $table->foreign('offer_id')->references('id')
                ->on('offers');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('audits');
    }
}

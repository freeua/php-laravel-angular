<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BikeConfiguratorPartner extends Migration
{
    public function up()
    {

        Schema::create('partner_portal', function(Blueprint $table) {
            $table->unsignedInteger('partner_id');
            $table->foreign('partner_id')->references('id')->on('partners');
            $table->unsignedInteger('portal_id');
            $table->foreign('portal_id')->references('id')->on('portals');
            $table->text('bike_configurator_url');
            $table->string('menu_text');
        });
    }

    public function down()
    {
        Schema::drop('partner_portals');
    }
}

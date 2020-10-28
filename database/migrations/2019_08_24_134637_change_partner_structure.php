<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangePartnerStructure extends Migration
{
    public function up()
    {
        \Schema::table('partners', function(Blueprint $table) {
            $table->text('bike_configurator_url');
            $table->text('info_iframe_url');
            $table->string('configurator_menu_text');
            $table->string('calculator_cid');
        });
        \Schema::table('portals', function(Blueprint $table) {
            $table->unsignedInteger('partner_id')->nullable();
            $table->foreign('partner_id')->references('id')->on('partners');
        });
        \DB::table('partner_portal')->get()->each(function ($partnerPortal) {
            $partner = \App\Partners\Models\Partner::find($partnerPortal->partner_id);
            $portal = \App\Models\Portal::find($partnerPortal->portal_id);
            $partner->bike_configurator_url = $partnerPortal->bike_configurator_url;
            $partner->info_iframe_url = $partnerPortal->info_iframe_url;
            $partner->configurator_menu_text = $partnerPortal->menu_text;
            $portal->partner_id = $partner->id;
            $partner->saveOrFail();
            $portal->saveOrFail();
        });
    }

    public function down()
    {
    }
}

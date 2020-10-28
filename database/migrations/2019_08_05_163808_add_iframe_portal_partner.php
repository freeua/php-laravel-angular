<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIframePortalPartner extends Migration
{
    public function up()
    {
        Schema::table('partner_portal', function(Blueprint $table) {
            $table->text('info_iframe_url');
        });
    }

    public function down()
    {
    }
}

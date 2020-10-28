<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPartnerIdAudits extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('audits', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->nullable(true)->change();
            $table->unsignedInteger('partner_id')->nullable();
            $table->foreign('partner_id')->on('partners')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('audits', function (Blueprint $table) {
            $table->dropForeign('audits_partner_id_foreign');
            $table->dropIndex('audits_partner_id_foreign');
            $table->dropColumn('partner_id');
        });
    }
}

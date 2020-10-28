<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyAuditsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('audits', function (Blueprint $table) {
            $table->morphs('model');
            $table->unsignedInteger('offer_id')->nullable()->change();
            $table->string('type')->nullable()->change();
            $table->integer('created_by')->unsigned()->nullable();
            $table->foreign('created_by')->references('id')->on('portal_users');
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
            $table->dropMorphs("model");
            $table->unsignedInteger('offer_id')->nullable(false)->change();
            $table->string('type')->nullable(false)->change();
            $table->dropForeign("audits_created_by_foreign");
            $table->dropColumn("created_by");
        });
    }
}

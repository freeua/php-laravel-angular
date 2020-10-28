<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddImprintAndPolicyToPortalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('portals', function (Blueprint $table) {
            $table->string('imprint_pdf')->nullable();
            $table->text('imprint')->nullable();
            $table->string('policy_pdf')->nullable();
            $table->text('policy')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('portals', function (Blueprint $table) {
            $table->dropColumn('imprint_pdf');
            $table->dropColumn('imprint');
            $table->dropColumn('policy_pdf');
            $table->dropColumn('policy');
        });
    }
}

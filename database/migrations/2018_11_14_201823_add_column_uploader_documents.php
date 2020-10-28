<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnUploaderDocuments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\Documents\Models\Document::query()->delete();
        \Schema::table('documents', function (Blueprint $table) {
            $table->unsignedInteger('uploader_id');
            $table->foreign('uploader_id')->references('id')->on('portal_users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign('documents_uploader_id_foreign');
            $table->dropIndex('documents_uploader_id_foreign');
            $table->dropColumn('uploader_id');
        });
    }
}

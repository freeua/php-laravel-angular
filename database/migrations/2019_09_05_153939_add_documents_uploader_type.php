<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDocumentsUploaderType extends Migration
{
    public function up()
    {
        \Schema::table('documents', function (Blueprint $table) {
            $table->string('uploader_type');
        });
        DB::table('documents')->update(['uploader_type' => \App\Portal\Models\User::class]);
    }

    public function down()
    {
        \Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn('uploader_type');
        });
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RefactorDocumentTypes extends Migration
{
    public function up()
    {
        Schema::table('documents', function(Blueprint $table) {
            $table->renameColumn('user_visibility', 'type')->nullable(false);
        });
        DB::table('documents')->where('filename', 'like', 'Ubernahmebestatigung%')
            ->update(['type' => \App\Documents\Models\Document::TAKEOVER_CERTIFICATE]);
        DB::table('documents')->where('filename', 'like', 'Gutschrift%')
            ->update(['type' => \App\Documents\Models\Document::CREDIT_NOTE]);
        DB::table('documents')->where('filename', 'like', 'Einzelleasingvertrag%')
            ->update(['type' => \App\Documents\Models\Document::SINGLE_LEASE]);
        DB::table('documents')->where('filename', 'like', 'Ueberlassungsvertrag%')
            ->update(['type' => \App\Documents\Models\Document::SIGNED_CONTRACT]);
        DB::table('documents')->where('filename', 'like', 'Rechnung%')
            ->update(['type' => \App\Documents\Models\Document::SUPPLIER_INVOICE]);
    }

    public function down()
    {
        Schema::table('documents', function(Blueprint $table) {
            $table->renameColumn('type', 'user_visibility')->nullable(true);
        });
    }
}

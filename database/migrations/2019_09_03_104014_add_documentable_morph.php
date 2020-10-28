<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Documents\Models\Document;

class AddDocumentableMorph extends Migration
{
    public function up()
    {
        \Schema::table('documents', function (Blueprint $table) {
            $table->nullableMorphs('documentable');

            $table->renameColumn('document_id', 'leasing_document_id');
            $table->renameColumn('document_type', 'leasing_document_type');
        });
        Document::query()->get()->each(function (Document $document) {
            $document->documentable()->associate($document->company);
        });
    }

    public function down()
    {
        \Schema::table('documents', function (Blueprint $table) {
            $table->dropMorphs('documentable');
            $table->renameColumn('leasing_document_id', 'document_id');
            $table->renameColumn('leasing_document_type', 'document_type');
        });
    }
}

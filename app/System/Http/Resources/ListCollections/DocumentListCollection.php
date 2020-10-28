<?php

namespace App\System\Http\Resources\ListCollections;

use App\Helpers\DateHelper;
use App\Helpers\FileSizeHelper;
use App\Http\Resources\BaseListCollection;
use App\Documents\Models\Document;
use Illuminate\Support\Collection;

/**
 * Class SupplierListCollection
 *
 * @package App\System\Http\Resources\ListCollections
 */
class DocumentListCollection extends BaseListCollection
{
    /**
     * Specifies data item in response
     *
     * @return Collection
     */
    protected function data(): Collection
    {
        return $this->collection->transform(function (Document $document) {
            $uploader = $document->uploader()->withTrashed()->first(['first_name', 'last_name']);
            $uploaderName = $uploader->first_name . ' ' . $uploader->last_name;
            return [
                'id' => $document->id,
                'filename' => $document->filename,
                'size' => FileSizeHelper::getHumanFileSize($document->size),
                'visible' => $document->visible,
                'date' => DateHelper::date($document->created_at),
                'uploader' => $uploaderName,
                'document_id' => $document->document_id,
                'document_type' => $document->document_type,
                'number' => !empty($document->document_id) ? $document->document()->pluck('number')->first() : null
            ];
        });
    }
}

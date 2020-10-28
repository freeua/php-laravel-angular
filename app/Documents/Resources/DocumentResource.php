<?php

namespace App\Documents\Resources;

use App\Helpers\DateHelper;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'filename' => $this->filename,
            'size' => $this->size,
            'visible' => $this->visible,
            'date' => $this->created_at,
            'uploader' => $this->uploader->first_name . ' ' . $this->uploader->last_name,
            'leasingDocumentId' => $this->leasing_document_id,
            'leasingDocumentType' => $this->leasing_document_type,
            'documentableId' => $this->documentable_id,
            'documentableType' => $this->documentable_type,
            'type' => $this->type,
        ];
    }
}

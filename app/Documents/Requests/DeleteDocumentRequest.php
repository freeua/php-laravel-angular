<?php

namespace App\Documents\Requests;

use App\Documents\Models\Document;
use App\Http\Requests\ApiRequest;
use App\Models\Companies\Company;
use App\Models\Portal;
use App\Portal\Models\User;
use App\System\Models\User as SystemUser;

class DeleteDocumentRequest extends ApiRequest
{
    public function authorize()
    {
        $document = $this->route('document');
        $user = auth()->user();
        if ($document->type != Document::INFORMATIVE) {
            return false;
        }
        if ($user instanceof User && $user->isAdmin()) {
            return $document->documentable instanceof Portal && $document->documentable->id == $user->portal_id;
        }
        if ($user instanceof User && $user->isCompanyAdmin()) {
            return $document->documentable instanceof Company && $document->documentable->id == $user->company_id;
        }
        if ($user instanceof SystemUser) {
            return true;
        }
        return false;
    }

    public function rules()
    {
        return [];
    }
}

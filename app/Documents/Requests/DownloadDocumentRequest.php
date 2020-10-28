<?php

namespace App\Documents\Requests;

use App\Documents\Models\Document;
use App\Http\Requests\ApiRequest;
use App\Models\Companies\Company;
use App\Models\Portal;
use App\Portal\Models\Supplier;
use App\Portal\Models\User;
use App\System\Models\User as SystemUser;

class DownloadDocumentRequest extends ApiRequest
{
    public function authorize()
    {
        $document = $this->route('document');
        if ($document instanceof Document) {
            return $this->checkUserFileAccess($document);
        }
        return false;
    }

    public function rules()
    {
        return [];
    }

    private function checkUserFileAccess(Document $document)
    {
        $user = auth()->user();
        if ($user instanceof User) {
            switch (true) {
                case $user->isAdmin():
                    return $this->checkPortalAdminAccess($document, $user);
                case $user->isSupplier():
                    return $this->checkSupplierAccess($document, $user);
                case $user->isCompanyAdmin():
                    return $this->checkCompanyAdminAccess($document, $user);
                case $user->isEmployee():
                    return $this->checkEmployeeAccess($document, $user);
            }
        } elseif ($user instanceof SystemUser) {
            return $this->checkSystemAccess($document);
        }
        return false;
    }

    private function checkPortalAdminAccess(Document $document, User $user)
    {
        if (is_null($document->documentable_id)) {
            return $document->type == Document::INFORMATIVE;
        } elseif ($document->documentable instanceof Portal && $document->documentable_id == $user->portal_id) {
            return true;
        } elseif ($document->documentable instanceof Company && $document->documentable->portal_id == $user->portal_id) {
            return true;
        }
        return false;
    }

    private function checkSupplierAccess(Document $document, User $user)
    {
        return $document->documentable instanceof Supplier && $document->documentable_id == $user->supplier_id;
    }

    private function checkCompanyAdminAccess(Document $document, User $user)
    {
        if (is_null($document->documentable_id)) {
            return $document->type == Document::INFORMATIVE;
        } elseif ($document->documentable instanceof Company && $document->documentable->portal_id == $user->portal_id) {
            return in_array($document->type, [Document::INFORMATIVE, Document::SIGNED_CONTRACT, Document::TAKEOVER_CERTIFICATE, Document::SINGLE_LEASE]);
        } elseif ($document->documentable instanceof Portal && $document->documentable_id == $user->portal_id) {
            return $document->type == Document::INFORMATIVE;
        }
        return false;
    }

    private function checkEmployeeAccess(Document $document, User $user)
    {
        if (is_null($document->documentable_id)) {
            return $document->type == Document::INFORMATIVE;
        } elseif ($document->documentable instanceof Company && $document->documentable->portal_id == $user->portal_id) {
            return in_array($document->type, [Document::INFORMATIVE, Document::SIGNED_CONTRACT, Document::TAKEOVER_CERTIFICATE]);
        } elseif ($document->documentable instanceof Portal && $document->documentable_id == $user->portal_id) {
            return $document->type == Document::INFORMATIVE;
        }
        return false;
    }

    private function checkSystemAccess(Document $document)
    {
        return is_null($document->documentable_id) || $document->type == Document::SUPPLIER_INVOICE;
    }
}

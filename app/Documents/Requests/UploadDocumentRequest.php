<?php

namespace App\Documents\Company;

use App\Http\Requests\ApiRequest;
use App\Portal\Models\User;
use App\System\Models\User as SystemUser;

class UploadDocumentRequest extends ApiRequest
{
    public function authorize()
    {
        $user = auth()->user();
        if ($user instanceof User) {
            return $user->isAdmin() || $user->isCompanyAdmin();
        }
        if ($user instanceof SystemUser) {
            return true;
        }
        return false;
    }

    public function rules()
    {
        return [
            'file' => 'required|file|mimes:pdf|max:25000',
            'filename' => 'required|string'
        ];
    }
}

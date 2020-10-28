<?php


namespace App\Documents\Requests;

use App\Helpers\PaginationHelper;
use App\Http\Requests\ApiRequest;

class DocumentListRequest extends ApiRequest
{

    public function rules()
    {
        return PaginationHelper::paginationRequest([
            'legalDocuments' => 'boolean'
        ]);
    }

    public function authorize()
    {
        return true;
    }
}

<?php


namespace App\Modules\TechnicalServices\Requests;

use App\Helpers\PaginationHelper;
use App\Http\Requests\ApiRequest;

class TechnicalServicesListRequest extends ApiRequest
{

    public function rules()
    {
        return PaginationHelper::paginationRequest([
            'serviceModality' => 'string|nullable'
        ]);
    }

    public function authorize()
    {
        return true;
    }
}

<?php

namespace App\Portal\Http\Requests\V1;

use App\Http\Requests\ApiRequest;

class SearchContractRequest extends ApiRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'number' => 'required|string',
        ];
    }
}

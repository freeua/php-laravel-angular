<?php

namespace App\Portal\Http\Requests\V1;

use App\Http\Requests\ApiRequest;

class CancelContractRequest extends ApiRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'end_date' => 'required|date',
            'cancellation_reason' => 'required|string',
        ];
    }
}

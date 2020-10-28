<?php

namespace App\Portal\Http\Requests\V1\Employee;

use App\Http\Requests\ApiRequest;

/**
 * Class GenerateOfferContractRequest
 *
 * @package App\Portal\Http\Requests\V1\Employee
 */
class GenerateOfferCertificateRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'order.number'                                 => 'required|string'
        ];
    }
}

<?php

namespace App\Modules\TechnicalServices\Requests;

use App\Http\Requests\ApiRequest;

/**
 * Class TechnicalRequestAcceptRequest
 *
 * @package App\Portal\Http\Requests\V1\Supplier
 */
class TechnicalServiceAcceptRequest extends ApiRequest
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
            'frameNumber' => 'required|string',
            'grossAmount' => 'required|numeric',
            'inspectionCode' => 'required|string',
        ];
    }
}

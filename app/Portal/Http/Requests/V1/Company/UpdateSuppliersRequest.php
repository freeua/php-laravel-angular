<?php

namespace App\Portal\Http\Requests\V1\Company;

use App\Http\Requests\ApiRequest;

/**
 * Class UpdateSuppliersRequest
 *
 * @package App\Portal\Http\Requests\V1
 */
class UpdateSuppliersRequest extends ApiRequest
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
            'ids'   => 'required|array',
            'ids.*' => 'nullable|integer'
        ];
    }
}

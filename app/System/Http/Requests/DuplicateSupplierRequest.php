<?php

namespace App\System\Http\Requests;

use App\Http\Requests\ApiRequest;
use App\Portal\Models\Supplier;
use App\System\Rules\DuplicateToPortal;

/**
 * Class DuplicateSupplierRequest
 *
 * @package App\System\Http\Requests
 */
class DuplicateSupplierRequest extends ApiRequest
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
            'portal_id' => [
                'bail',
                'required',
                'integer',
                'exists:portals,id,deleted_at,NULL'
            ]
        ];
    }
}

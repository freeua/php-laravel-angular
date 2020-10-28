<?php

namespace App\Portal\Http\Requests\V1\Employee;

use App\Http\Requests\ApiRequest;
use Illuminate\Validation\Rule;

/**
 * Class SupplierListRequest
 *
 * @package App\Portal\Http\Requests\V1\Employee
 */
class SupplierListRequest extends ApiRequest
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
            'cities'       => 'array',
            'cities.*'     => 'integer',
            'categories'   => 'array',
            'categories.*' => 'integer',
            'order_by'     => 'string|nullable',
            'order'        => ['string', Rule::in(['asc', 'desc'])],
            'per_page'     => 'integer|nullable',
            'page'         => 'integer|nullable',
        ];
    }
}

<?php

namespace App\Portal\Http\Requests\V1;

use App\Http\Requests\ApiRequest;
use App\Portal\Models\Order;
use Illuminate\Validation\Rule;

/**
 * Class GetSupplierOrdersRequest
 *
 * @package App\Portal\Http\Requests\V1
 */
class GetSupplierOrdersRequest extends ApiRequest
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
            'status' => ['numeric', Rule::in(Order::getStatuses()->pluck('id'))],
        ];
    }
}

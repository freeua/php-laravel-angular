<?php

namespace App\System\Http\Requests;

use App\Http\Requests\ApiRequest;
use App\Portal\Models\Order;
use Illuminate\Validation\Rule;

/**
 * Class UpdateOrderRequest
 *
 * @package App\System\Http\Requests
 */
class UpdateOrderRequest extends ApiRequest
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
            'agreed_purchase_price'     => 'required|numeric',
            'leasing_rate'              => 'required|numeric',
            'insurance'                 => 'required|numeric',
            'calculated_residual_value' => 'required|numeric',
            'leasing_period'            => 'required|integer',
            'product_size'              => 'string',
            'status_id'                    => ['required', Rule::in(Order::getStatuses())],
        ];
    }
}

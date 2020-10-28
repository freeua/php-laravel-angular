<?php

namespace App\Portal\Http\Requests\V1\Supplier;

use App\Http\Requests\ApiRequest;
use App\Portal\Models\Role;
use App\Portal\Rules\HasRole;

/**
 * Class PickupOrderRequest
 *
 * @package App\Portal\Http\Requests\V1\Supplier
 */
class PickupOrderRequest extends ApiRequest
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
        $orderId = $this->route('order')->id;
        $companyId = $this->route('order')->company_id;

        return [
            'pickup_code' => 'required|string|exists:system.orders,pickup_code,deleted_at,NULL,id,' . $orderId,
            'card_issue_date' => 'required|string',
            'card_issue_authority' => 'required|string',
            'frame_number' => 'required|string',
        ];
    }
}

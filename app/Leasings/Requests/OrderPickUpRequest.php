<?php
namespace App\Leasings\Requests;

use App\Http\Requests\ApiRequest;
use App\Partners\Models\Partner;

class OrderPickUpRequest extends ApiRequest
{
    public function authorize()
    {
        $requester = request()->requester;
        $order = $this->route('order');
        if ($requester instanceof Partner && $order->partner != null) {
            return $order->partner->id == $requester->id;
        }
        return false;
    }

    public function rules()
    {
        $orderId = $this->route('order')->id;

        return [
            'pickupCode' => 'required|string',
            'idCard.issueDate' => 'required|string',
            'idCard.authority' => 'required|string',
            'serialNumber' => 'required|string',
        ];
    }
}

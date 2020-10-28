<?php
namespace App\Leasings\Requests;

use App\Http\Requests\ApiRequest;
use App\Partners\Models\Partner;

class OrderReadyRequest extends ApiRequest
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
        return [];
    }
}

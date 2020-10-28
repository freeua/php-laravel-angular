<?php
namespace App\Leasings\Requests;

use App\Http\Requests\ApiRequest;
use App\Partners\Models\Partner;

class OrderListRequest extends ApiRequest
{
    public function authorize()
    {
        $requester = request()->requester;
        return $requester instanceof Partner;
    }

    public function rules()
    {
        return [];
    }
}

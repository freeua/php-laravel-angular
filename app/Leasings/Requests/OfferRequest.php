<?php
namespace App\Leasings\Requests;

use App\Http\Requests\ApiRequest;
use App\Partners\Models\Partner;

class OfferRequest extends ApiRequest
{
    public function authorize()
    {
        $requester = request()->requester;
        $offer = $this->route('offer');
        if ($requester instanceof Partner && $offer->partner != null) {
            return $offer->partner->id == $requester->id;
        }
        return false;
    }

    public function rules()
    {
        return [];
    }
}

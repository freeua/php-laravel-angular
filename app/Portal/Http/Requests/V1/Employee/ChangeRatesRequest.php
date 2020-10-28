<?php

namespace App\Portal\Http\Requests\V1\Employee;

use App\Http\Requests\ApiRequest;
use App\Portal\Helpers\AuthHelper;

/**
 * Class SearchRequest
 *
 * @package App\Portal\Http\Requests\V1\Employee
 */
class ChangeRatesRequest extends ApiRequest
{
    public function authorize()
    {
        /* @var $offer \App\Portal\Models\Offer */
        $offer = $this->route('offer');
        return $offer->user->id === AuthHelper::user()->id;
    }

    public function rules()
    {
        return [
            'insuranceRate.id' => 'required_without:serviceRate.id',
            'serviceRate.id' => 'required_without:insuranceRate.id',
        ];
    }
}

<?php

namespace App\Portal\Http\Requests\V1;

use App\Http\Requests\ApiRequest;
use App\Modules\TechnicalServices\Models\TechnicalService;
use Illuminate\Validation\Rule;

/**
 * Class GetSupplierTechnicalServicesRequest
 *
 * @package App\Portal\Http\Requests\V1
 */
class GetSupplierTechnicalServicesRequest extends ApiRequest
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
            'status' => ['numeric', Rule::in(TechnicalService::getStatuses()->pluck('id'))],
        ];
    }
}

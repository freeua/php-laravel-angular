<?php

namespace App\Portal\Http\Requests\V1;

use App\Http\Requests\ApiRequest;
use App\Portal\Models\Supplier;
use Illuminate\Validation\Rule;

/**
 * Class UpdateSupplierRequest
 *
 * @package App\Portal\Http\Requests\V1
 */
class UpdateSupplierRequest extends ApiRequest
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
        $city_id = $this->request->get('city_id');
        return [
            'name' => 'required|string|max:190',
            'admin_email' => 'required|email',
            'vat' => 'required|string|max:20',
            'admin_first_name' => 'required|string',
            'admin_last_name' => 'required|string',
            'zip' => [
                        'required',
                        'string',
                        'max:20',
                        Rule::exists('postal_codes', 'code')->where(function ($query) use ($city_id) {
                            $query->where('city_id', $city_id);
                        })
                     ],
            'city_id' => 'required|integer|exists:cities,id',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
            'blind_discount' => 'present|numeric',
            'status_id' => ['required', Rule::in(Supplier::getStatuses())],
            'gp_number' => 'required|string',
            'bank_account' => 'required|string',
            'bank_name' => 'required|string',
            'grefo' => 'required|string',
        ];
    }
}

<?php

namespace App\Portal\Http\Requests\V1;

use App\Http\Requests\ApiRequest;
use App\Portal\Models\Supplier;
use Illuminate\Validation\Rule;

/**
 * Class CreateSupplierRequest
 *
 * @package App\Portal\Http\Requests\V1
 */
class CreateSupplierRequest extends ApiRequest
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
            'name'             => 'required|string|unique:suppliers,name,NULL,id,deleted_at,NULL',
            'vat'              => 'required|string|max:15|unique:system.suppliers,vat,NULL,id,deleted_at,NULL',
            'admin_first_name' => 'required|string',
            'admin_last_name'  => 'required|string',
            'admin_email'      => 'required|email|unique:portal_users,email,NULL,id,deleted_at,NULL',
            'zip'              => [
                                    'required',
                                    'string',
                                    'max:20',
                                    Rule::exists('postal_codes', 'code')->where(function ($query) use ($city_id) {
                                        $query->where('city_id', $city_id);
                                    })
                                  ],
            'city_id'          => 'required|integer|exists:cities,id',
            'address'          => 'required|string',
            'phone'            => 'required|string|max:20',
            'status_id'        => ['required', Rule::in(Supplier::getStatuses())],
            'blind_discount'   => 'present|nullable|numeric',
            'gp_number'        => 'required|string',
            'bank_account'     => 'required|string',
            'bank_name'        => 'required|string',
            'grefo'            => 'required|string',
        ];
    }
}

<?php

namespace App\System\Http\Requests;

use App\Http\Requests\ApiRequest;
use App\Portal\Models\Supplier;
use Illuminate\Validation\Rule;

/**
 * Class CreateSupplierRequest
 *
 * @package App\System\Http\Requests
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

        $uniqueVat = Rule::unique('suppliers', 'vat')
            ->whereNull('deleted_at');
        $uniqueEmail = Rule::unique('portal_users', 'email');

        return [
            'name'             => 'required|string',
            'vat'              => ['required', 'string', 'max:15', $uniqueVat],
            'status_id'        => ['required', Rule::in([Supplier::STATUS_ACTIVE, Supplier::STATUS_INACTIVE])],
            'city_id'          => 'required|integer|exists:cities,id',
            'portal_id'        => 'required|integer|exists:portals,id,deleted_at,NULL',
            'address'          => 'required|string',
            'phone'            => 'required|string',
            'admin_first_name' => 'required|string',
            'admin_last_name'  => 'required|string',
            'admin_email'      => ['required_with:portal_id', 'email', $uniqueEmail],
            'gp_number'        => 'required|string',
            'bank_account'     => 'required|string',
            'bank_name'        => 'required|string',
            'grefo'            => 'required|string',
        ];
    }
}

<?php

namespace App\System\Http\Requests;

use App\Http\Requests\ApiRequest;
use App\System\Rules\UniqueInPortal;
use App\Portal\Models\Supplier;
use Illuminate\Validation\Rule;

/**
 * Class UpdateSupplierRequest
 *
 * @package App\System\Http\Requests
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
        $supplierId = $this->route('supplier')->id;
        $portalId = $this->get('portal_id');

        return [
            'name'               => 'required|string',
            'vat'                => 'required|string|max:15|unique:suppliers,vat,' . $supplierId . ',id,deleted_at,NULL',
            'status_id'             => ['required', Rule::in([Supplier::STATUS_ACTIVE, Supplier::STATUS_INACTIVE])],
            'city_id'            => 'required|integer|exists:cities,id',
            'address'            => 'required|string',
            'phone'              => 'required|string',
            'admin_first_name'   => 'required|string',
            'admin_last_name'    => 'required|string',
            'admin_email'        => [
                'required_with:portal_id',
                'email',
                new UniqueInPortal($portalId, 'users', 'email', $supplierId)
            ],
            'gp_number'        => 'required|string',
            'bank_account'     => 'required|string',
            'bank_name'        => 'required|string',
            'grefo'            => 'required|string',
        ];
    }
}

<?php

namespace App\Portal\Http\Requests\V1;

use App\Http\Requests\ApiRequest;
use App\Portal\Models\Supplier;
use Illuminate\Validation\Rule;

/**
 * Class ImportSupplierRequest
 *
 * @package App\Portal\Http\Requests\V1
 */
class ImportSupplierRequest extends ApiRequest
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
            'companies.*.id'               => 'required|integer|exists:companies,id',
            'suppliers.*.name'             => 'required|distinct|string|unique:suppliers,name,NULL,id,deleted_at,NULL',
            'suppliers.*.vat'              => 'required|distinct|string|max:15|unique:system.suppliers,vat,NULL,id,deleted_at,NULL',
            'suppliers.*.admin_first_name' => 'required|string',
            'suppliers.*.admin_last_name'  => 'required|string',
            'suppliers.*.admin_email'      => 'required|distinct|email|unique:portal_users,email,NULL,id,deleted_at,NULL',
            'suppliers.*.zip'              => 'required|string|max:20|exists:postal_codes,code',
            'suppliers.*.city.name'        => 'required|string|exists:cities,name',
            'suppliers.*.address'          => 'required|string',
            'suppliers.*.phone'            => 'required|string|max:20',
            'suppliers.*.status_id'        => ['required', Rule::in(Supplier::getStatuses())],
            'suppliers.*.blind_discount'   => 'present|nullable|numeric',
            'suppliers.*.gp_number'        => 'required|string',
            'suppliers.*.bank_account'     => 'required|string',
            'suppliers.*.bank_name'        => 'required|string',
            'suppliers.*.grefo'            => 'required|string',
        ];
    }
}

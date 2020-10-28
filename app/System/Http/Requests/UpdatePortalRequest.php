<?php

namespace App\System\Http\Requests;

use App\Http\Requests\ApiRequest;
use App\Models\Portal;
use Illuminate\Validation\Rule;

/**
 * Class UpdatePortalRequest
 *
 * @package App\System\Http\Requests
 */
class UpdatePortalRequest extends ApiRequest
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
        $portalId = $this->route('portal')->id;

        return [
            'name' => 'required|string',
            'domain' => 'required|string|unique:portals,domain,' . $portalId . ',id,deleted_at,NULL',
            'status_id' => ['required', Rule::in([Portal::STATUS_ACTIVE, Portal::STATUS_INACTIVE])],
            'admin_first_name' => 'required|string',
            'admin_last_name' => 'required|string',
            'admin_email' => 'required|email|unique:portals,admin_email,' . $portalId . ',id,deleted_at,NULL',
            'company_name' => 'required|string',
            'company_city_id' => 'required|integer|exists:cities,id',
            'company_address' => 'required|string',
            'autoresponderText' => 'present|string',
            'company_vat' => 'required|string|max:15|unique:portals,company_vat,' . $portalId . ',id,deleted_at,NULL',
        ];
    }
}

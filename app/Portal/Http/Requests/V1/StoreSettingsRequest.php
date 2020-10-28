<?php

namespace App\Portal\Http\Requests\V1;

use App\Helpers\PortalHelper;
use App\Http\Requests\ApiRequest;
use App\Rules\Either;
use App\Models\Portal;
use Illuminate\Validation\Rule;

/**
 * Class StoreSettingsRequest
 *
 * @package App\Portal\Http\Requests\V1
 */
class StoreSettingsRequest extends ApiRequest
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
        $portalId = PortalHelper::id();
        $city_id = $this->request->get('company_city_id');
        return [
            'admin_first_name'                       => 'required|string',
            'admin_last_name'                        => 'required|string',
            'company_name'                           => 'required|string',
            'company_zip'                            => [
                                                            'required',
                                                            'string',
                                                            'max:20',
                                                            Rule::exists('postal_codes', 'code')->where(function ($query) use ($city_id) {
                                                                $query->where('city_id', $city_id);
                                                            })
                                                        ],
            'company_city_id'                        => 'required|integer|exists:cities,id',
            'company_address'                        => 'required|string',
            'company_vat'                            => 'required|string|max:15|unique:system.portals,company_vat,' . $portalId . ',id,deleted_at,NULL',
            'logo'                                   => [new Either(['image||max:5000', 'nullable|string'])],
            'color'                                  => 'required|string',
            'status_id'                              => ['required', Rule::in(Portal::getStatuses())],
            'leasing_settings'                       => 'required|array',
            'leasing_settings.*.id'                  => 'integer|exists:system.portal_leasing_settings,id,deleted_at,NULL,portal_id,' . $portalId,
            'leasing_settings.*.name'                => 'required|string|distinct',
            'leasing_settings.*.product_category_id' => 'required|integer|exists:product_categories,id,deleted_at,NULL',
            'leasing_settings.*.factor'              => 'required|numeric|between:0,99',
            'leasing_settings.*.period'              => 'required|integer|between:1,48',
            'leasing_settings.*.default'             => 'required|boolean',
            'leasing_settings.*.insurance'           => 'required|numeric|between:0,100',
            'leasing_settings.*.service_rate'        => 'required|numeric|between:0,99999',
            'leasing_settings.*.residual_value'      => 'present|nullable|numeric|between:0,99',
        ];
    }
}

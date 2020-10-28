<?php

namespace App\Portal\Http\Requests\V1\Supplier;

use App\Portal\Helpers\AuthHelper;
use App\Http\Requests\ApiRequest;
use App\Rules\Either;

/**
 * Class StoreSettingsRequest
 *
 * @package App\Portal\Http\Requests\V1\Supplier
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
        $supplierId = AuthHelper::user()->supplier_id;

        return [
            'logo'          => 'sometimes|image|max:5000',
            'color'         => 'required|string',
        ];
    }
}

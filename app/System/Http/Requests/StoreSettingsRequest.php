<?php

namespace App\System\Http\Requests;

use App\Http\Requests\ApiRequest;
use App\Rules\Either;

/**
 * Class StoreSettingsRequest
 *
 * @package App\System\Http\Requests
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
        return [
            'domain'          => 'required|string',
            'email'           => 'required|email',
            'logo'            => ['present', new Either(['image|max:5000', 'nullable|string'])],
            'leasingable_pdf' => ['present', new Either(['file|mimes:pdf|max:2048', 'nullable|string'])],
            'color'           => 'string',
        ];
    }
}

<?php

namespace App\Portal\Http\Requests\V1;

use App\Http\Requests\ApiRequest;

/**
 * Class CompanySlugExistsRequest
 *
 * @package App\Portal\Http\Requests\V1
 */
class CompanySlugExistsRequest extends ApiRequest
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
            'slug' => 'required|string',
        ];
    }
}

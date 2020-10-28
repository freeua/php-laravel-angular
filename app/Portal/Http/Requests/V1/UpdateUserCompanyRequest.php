<?php

namespace App\Portal\Http\Requests\V1;

use App\Http\Requests\ApiRequest;
use Illuminate\Validation\Rule;

/**
 * Class UpdateUserCompanyRequest
 *
 * @package App\Portal\Http\Requests\V1
 */
class UpdateUserCompanyRequest extends ApiRequest
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
        $rules = [
            'company.id' => 'required|integer|exists:companies,id',
            'company.name' => 'required|string|max:180'
        ];

        return $rules;
    }
}

<?php

namespace App\System\Http\Requests;

use App\Http\Requests\ApiRequest;

/**
 * Class CreateCompanyRequest
 *
 * @package App\Portal\Http\Requests\V1
 */
class CreateFaqCategoryRequest extends ApiRequest
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
            'name' => 'required|string|unique:faq_categories',
            'description' => 'required|string|max:300',
            'portal_id' => 'nullable|integer'
        ];
    }
}

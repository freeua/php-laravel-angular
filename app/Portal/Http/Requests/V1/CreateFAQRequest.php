<?php

namespace App\Portal\Http\Requests\V1;

use App\Http\Requests\ApiRequest;

/**
 * Class CreateCompanyRequest
 *
 * @package App\Portal\Http\Requests\V1
 */
class CreateFAQRequest extends ApiRequest
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
            'question' => 'required|string|unique:faqs',
            'answer' => 'required|string',
            'category_id' => 'required|integer|exists:faq_categories,id',
            'company_id' => 'nullable'
        ];
    }
}

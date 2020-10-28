<?php

namespace App\Portal\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class LeasingConditionRequest extends FormRequest
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
            'productCategory.id' => 'sometimes|integer|exists:product_categories,id,deleted_at,NULL',
            'factor' => 'required|numeric|between:0,99',
            'period' => 'required|integer|between:1,60',
            'residualValue' => 'required|nullable|numeric|between:0,99',
            'default' => 'boolean',
        ];
    }
}

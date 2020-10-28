<?php

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;

class CreateProductCategoryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string',
            'leasingConditions.*.factor' => 'required|numeric|between:0,100',
            'leasingConditions.*.residualValue' => 'required|numeric|between:0,100',
            'leasingConditions.*.period' => 'required|numeric|between:0,100',
            'insuranceRates.*.name' => 'required|string',
            'insuranceRates.*.minimum' => 'required|numeric|between:0,1000',
            'insuranceRates.*.amountType' => 'required|string',
            'insuranceRates.*.amount' => 'required|numeric|between:0,100',
            'serviceRates.*.name' => 'required|string',
            'serviceRates.*.minimum' => 'required|numeric|between:0,1000',
            'serviceRates.*.amountType' => 'required|string',
            'serviceRates.*.type' => 'required|string',
            'serviceRates.*.amount' => 'required|numeric|between:0,100',
        ];
    }
}

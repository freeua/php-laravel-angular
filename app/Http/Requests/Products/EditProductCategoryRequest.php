<?php

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;

class EditProductCategoryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string',
        ];
    }
}

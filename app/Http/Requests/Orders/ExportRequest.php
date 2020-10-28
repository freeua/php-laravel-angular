<?php

namespace App\Http\Requests\Orders;

use Illuminate\Foundation\Http\FormRequest;

class ExportRequest extends FormRequest
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
            'mlfinvoices' => 'required_without:name|boolean',
            'name' => 'required_without:mlfinvoices|string',
            'attachments' => 'required|string',
            'picked_up_at' => 'date',
        ];
    }
}

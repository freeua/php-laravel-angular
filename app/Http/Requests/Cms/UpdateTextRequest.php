<?php

namespace App\Http\Requests\Cms;

class UpdateTextRequest extends \App\Http\Requests\ApiRequest
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

    public function rules()
    {
        return [
            'id' => 'required|integer',
            'title' => 'string|nullable',
            'subtitle' => 'string|nullable',
            'key' => 'required|string',
            'description' => 'required|string',
            'portalId' => 'integer|nullable'
        ];
    }
}

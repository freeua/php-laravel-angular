<?php

namespace App\Portal\Http\Requests\V1;

use App\Http\Requests\ApiRequest;

/**
 * Class ViewWidgetRequest
 *
 * @package App\Portal\Http\Requests
 */
class ViewWidgetRequest extends ApiRequest
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
            'company_id' => 'nullable|integer',
            'date_from'  => 'required|integer',
            'date_to'    => 'required|integer'
        ];
    }
}

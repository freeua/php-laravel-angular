<?php

namespace App\System\Http\Requests;

use App\Http\Requests\ApiRequest;

/**
 * Class ViewWidgetRequest
 *
 * @package App\System\Http\Requests
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
            'portal_id' => 'required|integer|exists:portals,id,deleted_at,NULL',
            'date_from' => 'required|integer',
            'date_to' => 'required|integer',
        ];
    }
}

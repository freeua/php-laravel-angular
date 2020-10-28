<?php

namespace App\System\Http\Requests;

use App\Http\Requests\ApiRequest;

/**
 * Class UpdateWidgetsPositionRequest
 *
 * @package App\System\Http\Requests
 */
class UpdateWidgetsPositionRequest extends ApiRequest
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
            'positions' => 'required|array|between:1,3',
        ];
    }
}

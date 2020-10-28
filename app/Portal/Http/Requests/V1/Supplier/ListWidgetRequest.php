<?php

namespace App\Portal\Http\Requests\V1\Supplier;

use App\Http\Requests\ApiRequest;

/**
 * Class WidgetListRequest
 *
 * @package App\Portal\Http\Requests\Supplier
 */
class ListWidgetRequest extends ApiRequest
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
            'date_from' => 'required|integer',
            'date_to'   => 'required|integer',
        ];
    }
}

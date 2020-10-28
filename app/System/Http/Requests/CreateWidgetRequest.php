<?php

namespace App\System\Http\Requests;

use App\Http\Requests\ApiRequest;
use App\System\Models\Widget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * Class CreateWidgetRequest
 *
 * @package App\System\Http\Requests
 */
class CreateWidgetRequest extends ApiRequest
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
            'source'   => ['required', Rule::in(Widget::getSources()), 'unique:widgets,source,NULL,id,user_id,' . Auth::id()],
            'style'    => ['required', Rule::in(Widget::getStyles())],
            'position' => 'required|integer|between:1,3',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'source.unique' => __('validation.widget.source.unique'),
        ];
    }
}

<?php

namespace App\System\Http\Requests;

use App\Http\Requests\ApiRequest;
use App\System\Models\Widget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * Class UpdateWidgetRequest
 *
 * @package App\System\Http\Requests
 */
class UpdateWidgetRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->route('widget')->user_id == Auth::id();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'source' => [
                'required',
                Rule::in(Widget::getSources()),
                'unique:widgets,source,' . $this->route('widget')->id . ',id,user_id,' . Auth::id()
            ],
            'style'  => ['required', Rule::in(Widget::getStyles())]
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

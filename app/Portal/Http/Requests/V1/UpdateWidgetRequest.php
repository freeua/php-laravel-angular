<?php

namespace App\Portal\Http\Requests\V1;

use App\Http\Requests\ApiRequest;
use App\Portal\Helpers\AuthHelper;
use App\Portal\Models\Widget;
use Illuminate\Validation\Rule;

/**
 * Class UpdateWidgetRequest
 *
 * @package App\Portal\Http\Requests
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
        return $this->route('widget')->user_id == AuthHelper::id();
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
                'unique:widgets,source,' . $this->route('widget')->id . ',id,user_id,' . AuthHelper::id()
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

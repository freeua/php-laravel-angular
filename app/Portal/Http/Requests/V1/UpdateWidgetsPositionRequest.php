<?php

namespace App\Portal\Http\Requests\V1;

use App\Http\Requests\ApiRequest;
use App\Portal\Services\WidgetService;
use Illuminate\Contracts\Validation\Validator;

/**
 * Class UpdateWidgetsPositionRequest
 *
 * @package App\Portal\Http\Requests
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

    /**
     * @param Validator $validator
     */
    public function withValidator(Validator $validator): void
    {
        if (!$validator->fails()) {
            $validator->after(function (Validator $validator) {
                if (!app()->make(WidgetService::class)->validateUser(array_keys($this->input('positions')))) {
                    $validator->errors()->add('positions', __('validation.widget.positions.invalid'));
                }
            });
        }
    }
}

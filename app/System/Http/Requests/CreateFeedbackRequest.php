<?php

namespace App\System\Http\Requests;

use App\Http\Requests\ApiRequest;

/**
 * Class CreateFeedbackRequest
 *
 * @package App\System\Http\Requests
 */
class CreateFeedbackRequest extends ApiRequest
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
            'category_id' => 'required|integer|exists:feedback_categories,id',
            'body' => 'required|string',
        ];
    }
}

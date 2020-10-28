<?php

namespace App\System\Http\Requests;

use App\Http\Requests\ApiRequest;

/**
 * Class CreateReportRequest
 *
 * @package App\System\Http\Requests
 */
class CreateReportRequest extends ApiRequest
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
            'categories.*' => 'required|integer|exists:report_categories,id',
            'body'         => 'required|string',
        ];
    }
}

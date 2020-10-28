<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 20.03.2019
 * Time: 12:22
 */

namespace App\Portal\Http\Requests\V1;

use App\Http\Requests\ApiRequest;

class HomepageRequest extends ApiRequest
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
            'items'   => 'required|array',
            'items.title' => 'required|string',
            'items.description' => 'required|string',
            'items.steps' => 'present|array',
            'items.logo' => 'nullable|string',
        ];
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 22.04.2019
 * Time: 10:41
 */

namespace App\Portal\Http\Requests\V1;

use App\Http\Requests\ApiRequest;

class UpdatePolicyRequest extends ApiRequest
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
            'policy_checked' => 'required|boolean'
        ];
    }
}

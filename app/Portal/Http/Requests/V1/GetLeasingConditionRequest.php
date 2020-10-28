<?php
/**
 * Created by PhpStorm.
 * User: jpicornell
 * Date: 18/10/2018
 * Time: 21:48
 */

namespace App\Portal\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class GetLeasingConditionRequest extends FormRequest
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
            'name'                => 'string',
            'default'             => 'numeric',
        ];
    }
}

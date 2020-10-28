<?php

namespace App\Portal\Http\Requests\V1;

use App\Http\Requests\ApiRequest;
use App\Portal\Models\User;
use App\Portal\Models\Role;
use Illuminate\Validation\Rule;

/**
 * Class UpdateUserRequest
 *
 * @package App\Portal\Http\Requests\V1
 */
class UpdateFAQRequest extends ApiRequest
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
            'question' => 'required|string',
            'answer' => 'required|string',
            'category_id' => 'required|integer',
            'company_id' => 'nullable'
        ];
    }
}

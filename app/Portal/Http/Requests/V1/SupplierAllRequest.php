<?php

namespace App\Portal\Http\Requests\V1;

use App\Http\Requests\ApiRequest;
use App\Portal\Models\Supplier;
use Illuminate\Validation\Rule;

/**
 * Class SupplierAllRequest
 *
 * @package App\Portal\Http\Requests\V1
 */
class SupplierAllRequest extends ApiRequest
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
            'status' => ['nullable', Rule::in(Supplier::getStatuses())],
        ];
    }
}

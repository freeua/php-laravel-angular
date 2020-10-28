<?php

namespace App\Portal\Http\Requests\V1\Supplier;

use App\Http\Requests\ApiRequest;
use App\Portal\Models\Role;
use App\Portal\Rules\HasRole;

/**
 * Class PickupOrderRequest
 *
 * @package App\Portal\Http\Requests\V1\Supplier
 */
class UploadInvoiceRequest extends ApiRequest
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
            'invoice_file' => 'required|string|max:3000000'
        ];
    }
}

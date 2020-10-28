<?php

namespace App\Portal\Http\Requests\V1\Employee;

use App\Http\Requests\ApiRequest;

/**
 * Class UploadOfferContractRequest
 *
 * @package App\Portal\Http\Requests\V1\Employee
 */
class UploadOfferContractRequest extends ApiRequest
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
        ini_set('memory_limit', '256M');
        ini_set('post_max_size', '30M');
        ini_set('upload_max_filesize', '30M');
        ini_set('max_input_time', '100');
        return [
            'file' => 'required|file|mimes:pdf|max:25000'
        ];
    }
}

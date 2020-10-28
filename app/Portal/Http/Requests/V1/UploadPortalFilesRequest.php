<?php

namespace App\Portal\Http\Requests\V1;

use App\Http\Requests\ApiRequest;
use App\Rules\Either;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateCompanyRequest
 *
 * @package App\Portal\Http\Requests\V1
 */
class UploadPortalFilesRequest extends FormRequest
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
            'logo' => 'required_without_all:leasingablePdf,servicePdf,imprintPdf,policyPdf|image|max:5000',
            'leasingablePdf' => 'required_without_all:logo,imprintPdf,servicePdf,policyPdf|file|mimes:pdf|max:2048',
            'servicePdf' => 'required_without_all:logo,imprintPdf,leasingablePdf,policyPdf|file|mimes:pdf|max:2048',
            'imprintPdf' => 'required_without_all:logo,leasingablePdf,servicePdf,policyPdf|file|mimes:pdf|max:2048',
            'policyPdf' => 'required_without_all:logo,leasingablePdf,servicePdf,imprintPdf|file|mimes:pdf|max:2048',
        ];
    }
}

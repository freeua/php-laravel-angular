<?php

namespace App\Http\Requests;

use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

abstract class ApiRequest extends FormRequest
{
    const COMPANY_ADMIN = 'company';
    const EMPLOYEE = 'employee';
    const PORTAL_ADMIN = 'portal';
    const SUPPLIER = 'supplier';
    const SYSTEM = 'system';


    abstract public function rules();

    abstract public function authorize();

    public function getModule()
    {
        return $this->header('X-Benefit-Portal-Module');
    }

    protected function failedValidation(Validator $validator)
    {
        throw ValidationException::withMessages($validator->errors()->toArray());
    }
}

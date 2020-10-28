<?php

namespace App\Http\Requests;

use App\Helpers\PortalHelper;
use App\Portal\Helpers\AuthHelper;
use Illuminate\Validation\Rule;

class InsuranceRateRequest extends ApiRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        return [
            'name' => ['required', Rule::unique('insurance_rates')->where(function ($query) {
                $company = $this->route('company');
                $portal = $this->route('portal');
                $productCategory = request()->input('productCategory');
                $insuranceName = request()->input('name');
                $insuranceRate = $this->route('insuranceRate');
                if ($company) {
                    $query
                        ->where('company_id', $company->id);
                }
                if ($portal) {
                    $query
                        ->where('portal_id', $portal->id);
                }
                $query

                    ->where('product_category_id', $productCategory['id'])
                    ->where('name', $insuranceName);
                if (!empty($insuranceRate)) {
                    $query->where('id', '!=', $insuranceRate->id);
                }

                return $query;
            })],
            'amountType' => ['required', Rule::in(['fixed', 'percentage'])],
            'amount' => ['required', 'numeric', function ($attribute, $value, $fail) {
                if (request()->input('amountType') === 'fixed' && ($value > 100000 || $value < 0)) {
                    $fail('Ein fester Betrag muss eine gültige Nummer sein');
                }
                if (request()->input('amountType') === 'percentage' && ($value > 100 || $value < 0)) {
                    $fail('Ein fester Betrag muss eine gültige Nummer sein');
                }
            },],
            'minimum' => 'required|numeric|between:0,10000',
            'productCategory.id' => 'sometimes|numeric',
            'default' => 'sometimes|nullable|boolean',
        ];
    }
}

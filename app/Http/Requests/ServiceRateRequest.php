<?php

namespace App\Http\Requests;

use App\Models\Rates\ServiceRate;
use Illuminate\Validation\Rule;

class ServiceRateRequest extends ApiRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        return [
            'name' => ['required', Rule::unique('service_rates')->where(function ($query) {
                $portal = $this->route('portal');
                $company = $this->route('company');
                $productCategory = request()->input('productCategory');
                $serviceRate = $this->route('serviceRate');
                if ($portal) {
                    $query
                        ->where('portal_id', $portal->id);
                }
                if ($company) {
                    $query
                        ->where('company_id', $company->id);
                }
                $query
                    ->where('product_category_id', $productCategory['id']);
                if (!empty($serviceRate)) {
                    $query->where('id', '!=', $serviceRate->id);
                }

                return $query;
            })],
            'amountType' => ['required', Rule::in(['fixed', 'percentage'])],
            'type' => ['required', Rule::in([ServiceRate::INSPECTION, ServiceRate::FULL_SERVICE])],
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
            'budget' => 'required|numeric',
        ];
    }
}

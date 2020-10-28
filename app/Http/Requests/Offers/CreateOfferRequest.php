<?php

namespace App\Http\Requests\Offers;

use App\Portal\Helpers\AuthHelper;
use App\Portal\Models\Offer;
use App\Portal\Models\Role;
use App\Portal\Models\User;
use App\Portal\Rules\HasRole;
use App\Portal\Rules\UserExists;
use App\Traits\UploadsFile;
use Carbon\Carbon;

class CreateOfferRequest extends \App\Http\Requests\ApiRequest
{
    private $baseRules = [
        'product.model' => 'required|string|max:180',
        'product.brand' => 'required|string|max:180',
        'product.color' => 'required|string|max:180',
        'product.size' => 'required|string|max:180',
        'product.categoryId' => 'required|integer|exists:product_categories,id,deleted_at,NULL',
        'pricing.listPrice' => 'required|numeric|min:0|max:100000000',
        'pricing.discountedPrice' => 'required|numeric|max:100000000',
        'pricing.discount' => 'numeric|nullable|min:0|max:99',

        'accessories' => 'present|array',
        'accessories.*.name' => 'required|string|max:180',
        'accessories.*.amount' => 'required|numeric|min:0|max:100000',
        'accessories.*.price' => 'required|numeric|min:0|max:10000000',
        'accessories.*.discount' => 'required|numeric|min:0|max:99',
    ];

    public function authorize()
    {
        $requestUser = AuthHelper::user();
        if ($requestUser->isSupplier()) {
            /** @var User $userOffer */
            $userOffer = User::query()->find($this->input('user.id'));
            if ($userOffer) {
                return in_array(
                    $userOffer->company_id,
                    $requestUser->supplier->companies->pluck('id')->toArray()
                );
            }
        }
        return $requestUser->isEmployee();
    }

    public function rules()
    {
        $user = AuthHelper::user();
        if ($user->isEmployee()) {
            return $this->employeeRules();
        }
        if ($user->isSupplier()) {
            return $this->supplierRules();
        }
    }

    private function employeeRules()
    {
        $offer = $this->route('offer');
        $offerPdf = null;
        if ($offer) {
            $offerPdf = $this->route('offer')->offerPdf;
        }

        return array_merge($this->baseRules, [
            'supplier.name' => 'required_without:supplier.id|string|max:180',
            'supplier.street' => 'required_without:supplier.id|string|max:180',
            'supplier.postalCode' => 'required_without:supplier.id|string|max:180',
            'supplier.city' => 'required_without:supplier.id|string|max:180',
            'supplier.id' => 'required_without:supplier.name,supplier.street,supplier.postalCode,supplier.city|numeric',
            'offerPdf' => $offerPdf ? '' : 'required|string|max:3000000',
            'statusId' => 'required|integer',
        ]);
    }

    private function supplierRules()
    {
        return array_merge($this->baseRules, [
            'expiryDate' => 'required|date|after:today',
            'deliveryDate' => 'required|date|after:today',

            'user.id' => [
                'required',
                'nullable',
                'integer',
                new UserExists('id'),
                new HasRole('id', Role::ROLE_EMPLOYEE),
            ],
        ]);
    }
}

<?php

namespace App\Leasings\Requests;

use App\Partners\Models\Partner;
use App\Portal\Models\Role;
use App\Portal\Models\User;
use App\Portal\Rules\HasRole;
use App\Portal\Rules\UserExists;

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
        $requester = request()->requester;
        if ($requester instanceof Partner) {
            $user = User::find(request()->input('user.id'));
            if (!is_null($user)) {
                return $requester->portals->keyBy('id')->has($user->portal_id);
            }
            return false;
        }
        if ($requester instanceof User) {
            return $requester->isSupplier() || $requester->isEmployee();
        }
        return false;
    }

    public function rules()
    {
        $requester = request()->requester;

        if ($requester instanceof Partner) {
            return $this->partnerRules();
        }

        if ($requester instanceof User) {
            if ($requester->isEmployee()) {
                return $this->employeeRules();
            }
            if ($requester->isSupplier()) {
                return $this->supplierRules();
            }
        }
    }

    private function partnerRules()
    {
        $offer = $this->route('offer');
        $offerPdf = null;
        if ($offer) {
            $offerPdf = $offer->offerPdf;
        }
        return array_merge($this->baseRules, [
            'expiryDate' => 'required|date|after:today',
            'deliveryDate' => 'required|date|after:today',
            'supplier.name' => 'required|string|max:180',
            'supplier.email' => 'required|email',
            'supplier.phone' => 'required|string|min:7|max:100',
            'supplier.street' => 'required|string|max:180',
            'supplier.postalCode' => 'required|string|max:180',
            'supplier.city' => 'required|string|max:180',
            'supplier.country' => 'required|string|max:100',
            'supplier.taxId' => 'required|string|max:30',
            'supplier.bankAccount' => 'required|string|max:60',
            'supplier.bankName' => 'required|string|max: 190',
            'supplier.adminFullName' => 'required|string|max:190',
            'supplier.gpNumber' => 'string|max:30',
        ]);
    }

    private function employeeRules()
    {
        $offer = $this->route('offer');
        $offerPdf = null;
        if ($offer) {
            $offerPdf = $offer->offerPdf;
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

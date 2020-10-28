<?php

namespace App\Services\Offers;

use App\Exceptions\DisabledProductCategory;
use App\Exceptions\MaximumPriceError;
use App\Exceptions\MinimumPriceError;
use App\Exceptions\UserIsNotAllowed;
use App\Helpers\PortalHelper;
use App\Http\Requests\Offers\CreateOfferRequest;
use App\Models\Audit;
use App\Portal\Helpers\AuthHelper;
use App\Portal\Models\CompanyProductCategory;
use App\Portal\Models\Offer;
use App\Portal\Models\Supplier;
use App\Portal\Models\User;
use App\Portal\Notifications\Offer\OfferCreated;
use App\Portal\Notifications\Offer\OfferCreatedForCompanyAdmin;
use App\Traits\UploadsFile;
use Carbon\Carbon;
use \App\Portal\Services\Base\OfferService as BaseOfferService;
use App\Portal\Helpers\ContractPrices;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OfferService
{
    use UploadsFile;

    /** @var BaseOfferService */
    public $baseOfferService;

    public function __construct(
        BaseOfferService $baseOfferService
    ) {
        $this->baseOfferService = $baseOfferService;
    }

    public function create(CreateOfferRequest $request)
    {
        try {
            \DB::beginTransaction();
            $offer = $this->transformRequestToModel(new Offer(), $request);
            $offer->portal_id = PortalHelper::id();
            $offer->company_id = $offer->user->company_id;
            $this->processOffer($offer, $request);
            \DB::commit();
            return $offer;
        } catch (\Exception $exception) {
            \DB::rollBack();
            throw $exception;
        }
    }

    public function edit(Offer $offer, CreateOfferRequest $request)
    {
        try {
            \DB::beginTransaction();
            $offer = $this->transformRequestToModel($offer, $request);
            $this->processOffer($offer, $request);
            \DB::commit();
            return $offer;
        } catch (\Exception $exception) {
            \DB::rollBack();
            throw $exception;
        }
    }

    private function processOffer(Offer $offer, CreateOfferRequest $request)
    {
        $this->fillRates($offer, $request);
        $this->fillPrices($offer, $request);
        $this->fillSubsidies($offer);
        $this->checkPrices($offer);
        $this->checkEnableProductCategory($offer);
        $this->checkIsAcceptOffer($offer);
        if (!$request->has('expiryDate')) {
            $offer->expiryDate = Carbon::now()->addMonth();
        }
        $this->handleBlindDiscount($offer);

        if ($request->has('offerPdf')) {
            $offer->offerPdf = UploadsFile::handlePrivateJsonFile($request->input('offerPdf'), "offers/{$offer->id}", "Angebot_Lieferant.pdf");
        }
        $offer->saveOrFail();
        $this->fillAccessories($offer, $request);
        $offer->save();
        if ($offer->status_id != Offer::STATUS_DRAFT && AuthHelper::user()->isEmployee()) {
            Audit::offerCreatedByEmployee($offer, AuthHelper::user());
            $offer = $this->baseOfferService->accept($offer);
        }
        if (AuthHelper::user()->isSupplier()) {
            Audit::offerCreatedBySupplier($offer, AuthHelper::user());
        }
        $this->notify($offer);
    }

    private function transformRequestToModel(Offer $offer, CreateOfferRequest $request): Offer
    {
        $validated = $request->validated();
        $offer = $this->buildBaseOffer($offer, $validated);
        if (isset($validated['statusId']) && $validated['statusId'] == Offer::STATUS_DRAFT) {
            $offer->status_id = Offer::STATUS_DRAFT;
        } else {
            $offer->status_id = Offer::STATUS_PENDING;
        }
        if (AuthHelper::user()->isSupplier()) {
            /** @var User $offerUser */
            $offerUser = User::query()->find($request->input('user.id'));
            $offer->supplier_id = AuthHelper::supplierId();
            $offer->sender()->associate(AuthHelper::user());
            $this->fillEmployeeInfo($offer, $offerUser);
            $offer = $this->fillFromSupplier($offer, $request);
        }
        if (AuthHelper::user()->isEmployee()) {
            $offerUser = AuthHelper::user();
            $this->fillEmployeeInfo($offer, $offerUser);
            $offer = $this->fillFromEmployee($offer, $request);
        }
        return $offer;
    }

    private function buildBaseOffer(Offer $offer, array $validated): Offer
    {
        $offer->productModel = $validated['product']['model'];
        $offer->productBrand = $validated['product']['brand'];
        $offer->productColor = $validated['product']['color'];
        $offer->productSize = $validated['product']['size'];
        $offer->productDiscount = $validated['pricing']['discount'] ? $validated['pricing']['discount'] : 0;
        $offer->productDiscountedPrice = $validated['pricing']['discountedPrice'];
        $offer->productListPrice = $validated['pricing']['listPrice'];
        $offer->productCategoryId = $validated['product']['categoryId'];
        return $offer;
    }

    private function fillRates(Offer $offer, CreateOfferRequest $request)
    {
        $offer->insuranceRate()->associate($offer->user->company
            ->insuranceRatesByProductCategoryId($request->input('product.categoryId'))->first());
        $offer->serviceRate()->associate($offer->user->company
            ->serviceRatesByProductCategoryId($request->input('product.categoryId'))->first());
    }

    private function fillPrices(Offer $offer, CreateOfferRequest $request)
    {
        $offer->accessoriesDiscountedPrice = $this->getAccessoriesDiscountedPrice($request->input('accessories'));
        $offer->accessoriesPrice = $this->getAccessoriesListPrice($request->input('accessories'));
        $offer->agreedPurchasePrice = $offer->productDiscountedPrice + $offer->accessoriesDiscountedPrice;
        $pricesHelper = new ContractPrices($offer);
        if (!$pricesHelper->getTotalRateWithCoverages()->isZero()) {
            $offer->taxRate = floor(($offer->productListPrice + $offer->accessoriesPrice) * 0.005);
        } else {
            $offer->taxRate = 0;
        }
    }

    private function checkIsAcceptOffer(Offer $offer)
    {
        if ($offer->status_id != Offer::STATUS_DRAFT && AuthHelper::user()->isEmployee() && $offer->company->is_accept_employee && !$offer->user->is_accept_offer) {
            throw new UserIsNotAllowed();
        }
    }

    private function checkPrices(Offer $offer)
    {
        $user = $offer->user;
        if ($user->individual_settings && $user->min_user_amount && $offer->agreedPurchasePrice < $user->min_user_amount) {
            throw new MinimumPriceError();
        }
        if (!$user->individual_settings && $offer->agreedPurchasePrice < $offer->company->min_user_amount) {
            throw new MinimumPriceError();
        }
        if ($user->individual_settings && $user->min_user_amount && $offer->agreedPurchasePrice > $user->max_user_amount) {
            throw new MaximumPriceError();
        }
        if (!$user->individual_settings && $offer->agreedPurchasePrice > $offer->company->max_user_amount) {
            throw new MaximumPriceError();
        }
    }

    private function checkEnableProductCategory(Offer $offer)
    {
        try {
            CompanyProductCategory::where('company_id', $offer->company->id)
                ->where('category_id', $offer->productCategoryId)
                ->where('status', true)
                ->firstOrFail();
        } catch (ModelNotFoundException $e) {
            throw new DisabledProductCategory();
        }
    }

    private function fillSubsidies(Offer $offer)
    {
        $pricesHelper = new ContractPrices($offer);
        $offer->leasingRateAmount = $pricesHelper->getLeasingRate()->getAmount()->toFloat();
        $offer->insuranceRateAmount = $pricesHelper->getInsuranceRate()->getAmount()->toFloat();
        $offer->serviceRateAmount = $pricesHelper->getServiceRate()->getAmount()->toFloat();
        $offer->leasingRateSubsidy = $pricesHelper->getLeasingRateCoverage()->getAmount()->toFloat();
        $offer->insuranceRateSubsidy = $pricesHelper->getInsuranceCoverage()->getAmount()->toFloat();
        $offer->serviceRateSubsidy = $pricesHelper->getServiceCoverage()->getAmount()->toFloat();
    }

    private function handleBlindDiscount($offer)
    {
        if (AuthHelper::supplier() && AuthHelper::supplier()->getBlindDiscount(PortalHelper::id())) {
            $blindDiscount = AuthHelper::supplier()->getBlindDiscount(PortalHelper::id());
            $offer->blindDiscountAmount = ($offer->productDiscountedPrice * ($blindDiscount / 100));
        }
    }

    private function fillAccessories(Offer $offer, CreateOfferRequest $request)
    {
        if (!empty($request->input('accessories'))) {
            $offer->fill(['accessories' => $request->input('accessories')]);
        } else {
            $offer->accessories()->delete();
        }
        if ($offer->accessories->count() > 0) {
            $offer->notes = "Inklusive Leasingfähiges Zubehör:";
            $offer->notes = $offer->accessories->reduce(function ($processedString, $accessory) {
                return $processedString . "\n\t - $accessory->amount x $accessory->name";
            }, $offer->notes);
        } else {
            $offer->notes = null;
        }
    }

    private function fillFromEmployee(Offer $offer, CreateOfferRequest $request)
    {
        if ($request->has('supplier.id')) {
            $offer->supplier_id = $request->input('supplier.id');
            /** @var Supplier $supplier */
            $supplier = Supplier::query()->findOrFail($request->input('supplier.id'));
            $offer->supplierName = $supplier->name;
            $offer->supplierCity = $supplier->city->name;
            $offer->supplierPostalCode = $supplier->zip;
            $offer->supplierStreet = $supplier->address;
            $offer->supplierCode = $supplier->code;
            $offer->supplierBankName = $supplier->bank_name;
            $offer->supplierBankAccount = $supplier->bank_account;
            $offer->supplierAdminName = $supplier->admin_first_name . ' ' . $supplier->admin_last_name;
            $offer->supplierPhone = $supplier->phone;
            $offer->supplierEmail = $supplier->admin_email;
            $offer->supplierTaxId = $supplier->vat;
            $offer->supplierGpNumber = $supplier->gp_number;
        } else {
            $offer->supplierName = $request->input('supplier.name');
            $offer->supplierCity = $request->input('supplier.city');
            $offer->supplierPostalCode = $request->input('supplier.postalCode');
            $offer->supplierStreet = $request->input('supplier.street');
            $offer->supplierCode = $request->input('supplier.code');
        }
        return $offer;
    }

    private function fillFromSupplier(Offer $offer, CreateOfferRequest $request)
    {
        $validated = $request->validated();
        $supplier = AuthHelper::supplier();
        $offer->expiryDate = new Carbon($validated['expiryDate']);
        $offer->deliveryDate = new Carbon($validated['deliveryDate']);
        $offer->senderName = AuthHelper::user()->fullName;
        $offer->senderId = AuthHelper::id();
        $offer->supplierName = $supplier->name;
        $offer->supplierCity = $supplier->city->name;
        $offer->supplierPostalCode = $supplier->zip;
        $offer->supplierStreet = $supplier->address;
        $offer->supplierCode = $supplier->code;
        $offer->supplierBankName = $supplier->bank_name;
        $offer->supplierBankAccount = $supplier->bank_account;
        $offer->supplierAdminName = $supplier->admin_first_name . ' ' . $supplier->admin_last_name;
        $offer->supplierPhone = $supplier->phone;
        $offer->supplierEmail = $supplier->admin_email;
        $offer->supplierTaxId = $supplier->vat;
        $offer->supplierGpNumber = $supplier->gp_number;
        return $offer;
    }

    private function fillEmployeeInfo(Offer $offer, User $offerUser)
    {
        $offer->user()->associate($offerUser);
        if ($offerUser->city) {
            $offer->employeeCity = $offerUser->city->name;
        }
        $offer->employeeName = $offerUser->fullName;
        $offer->employeeStreet = $offerUser->street;
        $offer->employeePostalCode = $offerUser->postal_code;
        $offer->employeePhone = $offerUser->phone;
        $offer->employeeEmail = $offerUser->email;
        $offer->employeeNumber = $offerUser->employee_number;
        $offer->employeeSalutation = $offerUser->salutation;
        $offer->employeeCode = $offerUser->code;
        return $offer;
    }

    private function notify($offer)
    {
        if (AuthHelper::user()->isSupplier()) {
            $offer->user->notify(new OfferCreated($offer));
            \Notification::send($offer->company->admins, new OfferCreatedForCompanyAdmin($offer));
        }
    }

    private function getAccessoriesDiscountedPrice(array $accessories)
    {
        return round(array_reduce($accessories, function ($prev, $accessory) {
            return $prev + $accessory['discounted_price'];
        }, 0), 2);
    }

    private function getAccessoriesListPrice(array $accessories)
    {
        return round(array_reduce($accessories, function ($prev, $accessory) {
            return $prev + ($accessory['amount'] * $accessory['price']);
        }, 0), 2);
    }
}

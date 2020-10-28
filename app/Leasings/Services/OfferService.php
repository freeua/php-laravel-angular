<?php

namespace App\Leasings\Services;

use App\Exceptions\DisabledProductCategory;
use App\Exceptions\MaximumPriceError;
use App\Exceptions\MinimumPriceError;
use App\Exceptions\UserIsNotAllowed;
use App\Helpers\PortalHelper;
use App\Leasings\Requests\CreateOfferRequest;
use App\Models\Audit;
use App\Portal\Models\CompanyProductCategory;
use App\Portal\Models\Offer;
use App\Portal\Models\Supplier;
use App\Portal\Models\User;
use App\Portal\Notifications\Offer\OfferCreated;
use App\Portal\Notifications\Offer\OfferCreatedForCompanyAdmin;
use App\Traits\UploadsFile;
use Carbon\Carbon;
use App\Portal\Services\Base\OfferService as BaseOfferService;
use App\Portal\Helpers\ContractPrices;
use App\Partners\Models\Partner;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OfferService
{
    use UploadsFile;

    public $baseOfferService;

    public function __construct(
        BaseOfferService $baseOfferService
    ) {
        $this->baseOfferService = $baseOfferService;
    }

    public function list()
    {
        $requester = request()->requester;
        if ($requester instanceof Partner) {
            return Offer::query()
                ->where('partner_id', $requester->id)
                ->with('company', 'status', 'productCategory', 'accessories', 'user')
                ->get();
        }
        return Offer::query()->get();
    }

    public function create(CreateOfferRequest $request)
    {
        try {
            \DB::beginTransaction();
            $offer = $this->transformRequestToModel(new Offer(), $request);
            $offer->portal_id = $offer->user->portal_id;
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
        $this->checkEnableProductCategory($offer);
        $this->checkPrices($offer);
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
        $this->storeAudits($offer);
        $requester = request()->requester;
        if ($requester instanceof User
            && $offer->status_id != Offer::STATUS_DRAFT
            && $requester->isEmployee()) {
            $offer = $this->baseOfferService->accept($offer);
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
        $requester = request()->requester;
        if ($requester instanceof User) {
            if ($requester->isSupplier()) {
                /** @var User $offerUser */
                $offerUser = User::query()->findOrFail($request->input('user.id'));
                $offer->supplier_id = $requester->supplier->id;
                $offer->supplier()->associate($requester);
                $this->fillEmployeeInfo($offer, $offerUser);
                $offer = $this->fillFromSupplier($offer, $request);
            }
            if ($requester->isEmployee()) {
                $this->fillEmployeeInfo($offer, $requester);
                $offer = $this->fillSupplierInfo($offer, $request);
            }
        } elseif ($requester instanceof Partner) {
            $offerUser = User::query()->findOrFail($request->input('user.id'));
            $offer->partner()->associate($requester);
            $offer->expiryDate = new Carbon($request->input('expiryDate'));
            $offer->deliveryDate = new Carbon($request->input('deliveryDate'));
            $this->fillEmployeeInfo($offer, $offerUser);
            $offer = $this->fillSupplierInfo($offer, $request);
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
        if ($offer->status_id != Offer::STATUS_DRAFT && $offer->user->isEmployee() && $offer->company->is_accept_employee && !$offer->user->is_accept_offer) {
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
            CompanyProductCategory::query()
                ->where('company_id', $offer->company->id)
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
        $requester = request()->requester;
        if ($requester instanceof User && $requester->isSupplier() && $requester->supplier->getBlindDiscount(PortalHelper::id())) {
            $blindDiscount = $requester->supplier->getBlindDiscount(PortalHelper::id());
            $offer->blindDiscountAmount = ($offer->productDiscountedPrice * ($blindDiscount / 100));
        }
    }

    private function fillAccessories(Offer $offer, CreateOfferRequest $request)
    {
        $offer->fill(['accessories' => $request->input('accessories')]);
        if ($offer->accessories->count() > 0) {
            if ($offer->notes) {
                $offer->notes .= "\n";
            }
            $offer->notes .= "Inklusive Leasingfähiges Zubehör:";
            $offer->notes = $offer->accessories->reduce(function ($processedString, $accessory) {
                return $processedString . "\n\t - $accessory->amount x $accessory->name";
            }, $offer->notes);
        }
    }

    private function storeAudits(Offer $offer)
    {
        $requester = request()->requester;
        if ($requester instanceof User) {
            if ($offer->status_id != Offer::STATUS_DRAFT && $requester()->isEmployee()) {
                Audit::offerCreatedByEmployee($offer, $requester);
            }
            if ($requester->isSupplier()) {
                Audit::offerCreatedBySupplier($offer, $requester);
            }
        } elseif ($requester instanceof Partner) {
            Audit::offerCreatedByPartner($offer, $requester);
        }
    }

    private function fillSupplierInfo(Offer $offer, CreateOfferRequest $request)
    {
        if ($request->has('supplier.id')) {
            $offer->supplier_id = $request->input('supplier.id');
            /** @var Supplier $supplier */
            $supplier = Supplier::query()->findOrFail($request->input('supplier.id'));
            $offer->supplierName = $supplier->name;
            $offer->supplierCity = $supplier->city->name;
            $offer->supplierPostalCode = $supplier->zip;
            $offer->supplierStreet = $supplier->address;
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
            $offer->supplierCountry = $request->input('supplier.country');
            $offer->supplierBankName = $request->input('supplier.bankName');
            $offer->supplierBankAccount = $request->input('supplier.bankAccount');
            $offer->supplierAdminName = $request->input('supplier.adminFullName');
            $offer->supplierPhone = $request->input('supplier.phone');
            $offer->supplierEmail = $request->input('supplier.email');
            $offer->supplierGpNumber = $request->input('supplier.gpNumber');
            $offer->supplierTaxId = $request->input('supplier.taxId');
        }
        return $offer;
    }

    private function fillFromSupplier(Offer $offer, CreateOfferRequest $request)
    {
        $validated = $request->validated();
        $supplier = request()->requester->supplier;
        $offer->expiryDate = new Carbon($validated['expiryDate']);
        $offer->deliveryDate = new Carbon($validated['deliveryDate']);
        $offer->senderName = request()->requester->fullName;
        $offer->supplierName = $supplier->name;
        $offer->supplierCity = $supplier->city->name;
        $offer->supplierPostalCode = $supplier->zip;
        $offer->supplierStreet = $supplier->address;
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
        return $offer;
    }

    private function notify($offer)
    {
        $requester = request()->requester;
        if (($requester instanceof User && $requester->isSupplier()) || $requester instanceof Partner) {
            $offer->user->notify(new OfferCreated($offer));
            \Notification::send($offer->company->admins, new OfferCreatedForCompanyAdmin($offer));
        }
    }

    private function getAccessoriesDiscountedPrice(array $accessories)
    {
        return round(array_reduce($accessories, function ($prev, $accessory) {
            return $prev + ($accessory['amount'] * $accessory['price'] * (1 - $accessory['discount'] / 100));
        }, 0), 2);
    }

    private function getAccessoriesListPrice(array $accessories)
    {
        return round(array_reduce($accessories, function ($prev, $accessory) {
            return $prev + ($accessory['amount'] * $accessory['price']);
        }, 0), 2);
    }
}

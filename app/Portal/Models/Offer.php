<?php

namespace App\Portal\Models;

use App\Models\Audit;
use App\Models\City;
use App\Models\Companies\Company;
use App\Models\ProductCategory;
use App\Models\Rates\InsuranceRate;
use App\Models\Rates\ServiceRate;
use App\Models\Status;
use App\Modules\TechnicalServices\Models\TechnicalService;
use App\Partners\Models\Partner;
use App\Traits\CamelCaseAttributes;
use App\Traits\HasGeneratedCode;
use App\Traits\HasStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use LaravelFillableRelations\Eloquent\Concerns\HasFillableRelations;

/**
 * @property int $id
 * @property string $number
 * @property int $userId
 * @property User $sender
 * @property string $senderName
 * @property int $product_id
 * @property int $company_id
 * @property int $portal_id
 * @property int $supplier_id
 * @property string $supplierName
 * @property string $supplierStreet
 * @property string $supplierPostalCode
 * @property string $supplierCity
 * @property string $supplierCode
 * @property string $supplierBankName
 * @property string $supplierBankAccount
 * @property string $supplierTaxId
 * @property string $supplierPhone
 * @property string $supplierEmail
 * @property string $supplierCountry
 * @property string $supplierGpNumber
 * @property string $supplierAdminName
 * @property string $employeeSalutation
 * @property string $employeeName
 * @property string $employeeStreet
 * @property string $employeePostalCode
 * @property string $employeeCity
 * @property string $employeeEmail
 * @property string $employeePhone
 * @property string $employeeNumber
 * @property string $employeeCode
 * @property float $productListPrice
 * @property float $productDiscountedPrice
 * @property float $accessoriesPrice
 * @property float $accessoriesDiscountedPrice
 * @property float $productDiscount
 * @property float $blindDiscountAmount
 * @property float $agreedPurchasePrice
 * @property float $taxRate
 * @property float $serviceRateAmount
 * @property float $insuranceRateAmount
 * @property float $leasingRateAmount
 * @property float $serviceRateSubsidy
 * @property float $insuranceRateSubsidy
 * @property float $leasingRateSubsidy
 * @property Carbon $expiryDate
 * @property Carbon $deliveryDate
 * @property string $productNotes
 * @property string $notes
 * @property array $contract_data
 * @property string $contract_file
 * @property string $offerPdf
 * @property Status $status
 * @property int $status_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property ProductCategory $productCategory
 * @property int $productCategoryId
 * @property string $productBrand
 * @property string $productModel
 * @property string $productColor
 * @property string $productSize
 * @property User $user
 * @property Company $company
 * @property User $supplierUser
 * @property Supplier $supplier
 * @property Order $order
 * @property Collection $accessories
 * @property InsuranceRate $insuranceRate
 * @property ServiceRate $serviceRate
 * @property integer $current_technical_service_id
 */
class Offer extends PortalModel
{
    use HasStatus, HasGeneratedCode, CamelCaseAttributes, HasFillableRelations;

    const STATUS_PENDING = 11;

    const STATUS_PENDING_APPROVAL = 18;

    const STATUS_ACCEPTED = 10;

    const STATUS_REJECTED = 9;

    const STATUS_DRAFT = 19;

    const STATUS_CONTRACT_APPROVED = 20;

    const EXPIRED_DAYS = 30;

    const ENTITY = 'offer';

    protected $dates = [
        'createdAt',
        'updatedAt',
        'expiryDate',
        'deliveryDate',
        'date',
    ];

    protected $fillable = [
        'contract_file',
        'number',
        'status_id',
        'status_updated_at',
        'productSize',
        'productColor',
        'productBrand',
        'productModel',
        'productListPrice',
        'productDiscountedPrice',
        'productDiscount',
        'productCategoryId',
        'agreedPurchasePrice',
        'sender',
        'supplierCity',
        'supplierPostalCode',
        'supplierName',
        'supplierStreet',
        'supplierPhone',
        'supplierCountry',
        'supplierBankAccount',
        'supplierBankName',
        'supplierPhone',
        'supplierEmail',
        'supplierTaxId',
        'supplierGpNumber',
        'supplierCode',
        'employeeCity',
        'employeePhone',
        'employeeEmail',
        'employeeNumber',
        'employeePostalCode',
        'employeeStreet',
        'employeeName',
        'employeeSalutation',
        'employeeCode',
        'insuranceRateAmount',
        'serviceRateAmount',
        'leasingRateAmount',
        'insuranceRateSubsidy',
        'serviceRateSubsidy',
        'leasingRateSubsidy',
        'taxRate',
        'offerPdf',
        'expiryDate',
        'deliveryDate',
        'current_technical_service_id',
    ];

    protected $fillable_relations = [
        'accessories',
        'serviceRate',
        'insuranceRate',
        'status',
        'partner',
    ];

    protected $attributes = [
        'status_id' => Offer::STATUS_PENDING,
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productCategory(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class)->withTrashed();
    }

    public function order(): HasOne
    {
        return $this->hasOne(Order::class);
    }

    public function technicalServices(): HasMany
    {
        return $this->hasMany(TechnicalService::class);
    }

    public function audits(): HasMany
    {
        return $this->hasMany(Audit::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function supplierCity(): BelongsTo
    {
        return $this->belongsTo(City::class, 'supplier_city_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class, 'partner_id');
    }

    public function accessories(): HasMany
    {
        return $this->hasMany(OfferAccessory::class);
    }

    public function insuranceRate(): BelongsTo
    {
        return $this->belongsTo(InsuranceRate::class);
    }

    public function serviceRate(): BelongsTo
    {
        return $this->belongsTo(ServiceRate::class);
    }

    public function getTotalRatesWithSubsidies(): float
    {
        return $this->leasingRateAmount + $this->insuranceRateAmount + $this->serviceRateAmount
            - $this->leasingRateSubsidy - $this->insuranceRateSubsidy - $this->serviceRateSubsidy;
    }

    public function hasAllContractFields()
    {
        return $this->employeeName && $this->employeePostalCode && $this->employeeCity && $this->employeeStreet
            && $this->employeePhone && $this->employeeSalutation && $this->employeeNumber
            && $this->employeeEmail;
    }

    public function reject()
    {
        $this->fill(['status_id' => Offer::STATUS_REJECTED, 'status_updated_at' => Carbon::now()]);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function document()
    {
        return $this->morphOne('App\Documents\Models\Document', 'leasing_document');
    }
}

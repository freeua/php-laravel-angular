<?php

namespace App\Portal\Models;

use App\Models\Companies\Company;
use App\Models\Portal;
use App\Models\ProductCategory;
use App\Models\Status;
use App\Modules\TechnicalServices\Models\TechnicalService;
use App\Traits\CamelCaseAttributes;
use App\Traits\HasGeneratedCode;
use App\Traits\HasStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $number
 * @property int $portal_id
 * @property int $order_id
 * @property int $user_id
 * @property int $product_id
 * @property int $supplier_id
 * @property int $company_id
 * @property string companyName
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
 * @property string sender
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
 * @property Carbon $startDate
 * @property Carbon $endDate
 * @property ProductCategory $productCategory
 * @property string $productBrand
 * @property string $productModel
 * @property string $productColor
 * @property string $productSize
 * @property Status $status
 * @property int $statusId
 * @property float $agreedPurchasePrice
 * @property float $taxRate
 * @property string $insuranceRateName
 * @property string $serviceRateName
 * @property string $insuranceRateAmount
 * @property string $serviceRateAmount
 * @property string $leasingRateAmount
 * @property float $leasingRate
 * @property float $insuranceRate
 * @property float $serviceRate
 * @property float $leasingRateSubsidy
 * @property float $insuranceRateSubsidy
 * @property float $serviceRateSubsidy
 * @property float $calculatedResidualValue
 * @property int $leasingPeriod
 * @property string $notes
 * @property Carbon $createdAt
 * @property Carbon $updatedAt
 * @property Carbon $deletedAt
 * @property Portal $portal
 * @property Supplier $supplier
 * @property Company $company
 * @property User $user
 * @property Product $product
 * @property Order $order
 * @property string $cancellation_reason
 * @property integer $current_technical_service_id
 * @property TechnicalService $technicalServices
 * @property string $serviceRateModality
 * @property double $serviceBudget
 * @property string $serialNumber
 */
class Contract extends PortalModel
{
    use SoftDeletes, CamelCaseAttributes, HasStatus, HasGeneratedCode;

    const STATUS_ACTIVE = 15;

    const STATUS_INACTIVE = 16;

    const STATUS_CANCELED = 21;

    const SERVICE_PRICE_TYPE_FIXED = 'Fixed';

    const SERVICE_PRICE_TYPE_PERCENTAGE = 'Percentage';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'start_date',
        'end_date',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    /**
     * @var array
     */
    protected $fillable = [
        'order_id',
        'number',
        'product_size',
        'product_color',
        'product_brand',
        'product_model',
        'product_list_price',
        'product_discounted_price',
        'product_discount',
        'product_category_id',
        'agreed_purchase_price',
        'accessories_discounted_price',
        'accessories_price',
        'sender',
        'supplier_city',
        'supplier_postal_code',
        'supplier_name',
        'supplier_street',
        'supplier_code',
        'supplier_country',
        'supplier_bank_account',
        'supplier_bank_name',
        'supplier_email',
        'supplier_phone',
        'supplier_tax_id',
        'supplier_gp_number',
        'employee_city',
        'employee_phone',
        'employee_email',
        'employee_number',
        'employee_postal_code',
        'employee_street',
        'employee_name',
        'employee_salutation',
        'employee_code',
        'insurance_rate_name',
        'service_rate_name',
        'insurance_rate_amount',
        'service_rate_amount',
        'leasing_rate_amount',
        'insurance_rate',
        'service_rate',
        'leasing_rate',
        'insurance_rate_subsidy',
        'service_rate_subsidy',
        'leasing_rate_subsidy',
        'calculated_residual_value',
        'tax_rate',
        'leasing_period',
        'notes',
        'status_id',
        'cancellation_reason',
        'current_technical_service_id',
        'end_date',
        'serviceRateModality',
        'serviceBudget',
        'serialNumber',
    ];

    protected $attributes = [
        'status_id' => Contract::STATUS_INACTIVE,
    ];

    public function isActive(): bool
    {
        return $this->startDate->isBefore(Carbon::now()) && $this->endDate->isAfter(Carbon::now());
    }

    public function isExpired(): bool
    {
        return $this->endDate->isBefore(Carbon::now());
    }

    public function portal(): BelongsTo
    {
        return $this->belongsTo(Portal::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productCategory(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class)->withTrashed();
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function technicalServices(): HasMany
    {
        return $this->hasMany(TechnicalService::class);
    }

    public function lastTechnicalService(): ?TechnicalService
    {
        return $this->hasMany(TechnicalService::class) ->orderBy('created_at', 'DESC')->first();
    }

    public function spentServiceBudget()
    {
        return $this->technicalServices()
            ->sum('gross_amount');
    }

    public function remainingServiceBudget()
    {
        return $this->serviceBudget - $this->spentServiceBudget();
    }
}

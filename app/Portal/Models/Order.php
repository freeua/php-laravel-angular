<?php

namespace App\Portal\Models;

use App\Documents\Models\Document;
use App\Models\City;
use App\Models\Companies\Company;
use App\Models\Portal;
use App\Models\ProductCategory;
use App\Models\Status;
use App\Modules\TechnicalServices\Models\TechnicalService;
use App\Partners\Models\Partner;
use App\Traits\CamelCaseAttributes;
use App\Traits\HasGeneratedCode;
use App\Traits\HasStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $number
 * @property int $portal_id
 * @property int $offer_id
 * @property int $supplier_id
 * @property int $user_id
 * @property int $product_id
 * @property int $company_id
 * @property string $companyName
 * @property string $productName
 * @property string $pickup_code
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
 * @property int $pickedUpBy
 * @property Carbon $pickedUpAt
 * @property Status $status
 * @property int $status_id
 * @property float $agreedPurchasePrice
 * @property float $taxRate
 * @property float $leasingRate
 * @property string $insuranceRateName
 * @property string $serviceRateName
 * @property string $insuranceRateAmount
 * @property string $serviceRateAmount
 * @property string $leasingRateAmount
 * @property float $insuranceRate
 * @property float $serviceRate
 * @property float $leasingRateSubsidy
 * @property float $insuranceRateSubsidy
 * @property float $serviceRateSubsidy
 * @property float $calculatedResidualValue
 * @property int $leasingPeriod
 * @property string $productSize
 * @property string $productBrand
 * @property string $productModel
 * @property string $productColor
 * @property string $notes
 * @property Carbon $date
 * @property Carbon $acceptedAt
 * @property Carbon $createdAt
 * @property Carbon $updatedAt
 * @property Carbon $deletedAt
 * @property Portal $portal
 * @property Supplier $supplier
 * @property Company $company
 * @property ProductCategory $productCategory
 * @property User $user
 * @property City $city
 * @property Offer $offer
 * @property Product $product
 * @property Contract $contract
 * @property string $takeoverFile
 * @property string $supplierOfferFile
 * @property string $invoiceFile
 * @property string $singleLeasingFile
 * @property string $invoice_file
 * @property integer $current_technical_service_id
 * @property string $creditNoteFile
 * @property boolean $creditNoteRead
 * @property string $serviceRateModality
 */
class Order extends Model
{
    use SoftDeletes, CamelCaseAttributes, HasStatus, HasGeneratedCode;

    const STATUS_OPEN = 14;

    const STATUS_READY = 12;

    const STATUS_SUCCESSFUL = 13;

    const STATUS_CANCELED_CONTRACT = 22;

    const PICKUP_CODE_LETTERS_COUNT = 3;

    const PICKUP_CODE_DIGITS_COUNT = 3;

    const SERVICE_PRICE_TYPE_FIXED = 'fixed';

    const SERVICE_PRICE_TYPE_PERCENTAGE = 'percentage';

    const ENTITY = 'order';

    protected $dates = [
        'date',
        'card_issue_date',
        'pickedUpAt',
        'acceptedAt',
        'picked_up_at',
        'accepted_at',
        'createdAt',
        'updatedAt',
        'deletedAt'
    ];

    protected $fillable = [
        'offer_id',
        'partner_id',
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
        'supplier_phone',
        'supplier_country',
        'supplier_bank_account',
        'supplier_bank_name',
        'supplier_email',
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
        'status_id',
        'pickup_code',
        'picked_up_by',
        'picked_up_at',
        'notes',
        'accepted_at',
        'tax_rate',
        'transferred',
        'card_issue_authority',
        'card_issue_date',
        'frame_number',
        'invoice_file',
        'creditNoteFile',
        'creditNoteRead',
        'current_technical_service_id',
        'serviceRateModality',
    ];

    protected $attributes = [
        'status_id' => Order::STATUS_OPEN,
    ];

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

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function productCategory(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class)->withTrashed();
    }

    public function contract(): HasOne
    {
        return $this->hasOne(Contract::class);
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }

    public function technicalServices(): HasMany
    {
        return $this->hasMany(TechnicalService::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function audits(): HasMany
    {
        return $this->offer->audits();
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function document()
    {
        return $this->morphOne(Document::class, 'leasing_document');
    }
}

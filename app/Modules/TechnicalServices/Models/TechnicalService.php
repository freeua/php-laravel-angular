<?php

namespace App\Modules\TechnicalServices\Models;

use App\Models\Audit;
use App\Models\City;
use App\Models\Companies\Company;
use App\Models\Portal;
use App\Models\Status;
use App\Partners\Models\Partner;
use App\Portal\Models\Contract;
use App\Portal\Models\Offer;
use App\Portal\Models\Order;
use App\Portal\Models\PortalModel;
use App\Portal\Models\Product;
use App\Portal\Models\Supplier;
use App\Portal\Models\User;
use App\Traits\CamelCaseAttributes;
use App\Traits\HasGeneratedCode;
use App\Traits\HasStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LaravelFillableRelations\Eloquent\Concerns\HasFillableRelations;

/**
 * @property int $id
 * @property string $number
 * @property int $portalId
 * @property int $companyId
 * @property int $productId
 * @property int $userId
 * @property int $productCategoryId
 * @property int $orderId
 * @property int $offerId
 * @property int $contractId
 * @property string $productSize
 * @property string $productColor
 * @property string $productModel
 * @property string $productBrand
 * @property string $employeeCity
 * @property string $employeePhone
 * @property string $employeeEmail
 * @property string $employeeNumber
 * @property string $employeePostalCode
 * @property string $employeeStreet
 * @property string $employeeName
 * @property string $employeeSalutation
 * @property string $supplierCity
 * @property string $supplierEmail
 * @property string $supplierPhone
 * @property string $supplierAdminName
 * @property string $supplierTaxId
 * @property string $supplierBankName
 * @property string $supplierBankAccount
 * @property string $supplierCountry
 * @property string $supplierPostalCode
 * @property string $supplierStreet
 * @property string $supplierName
 * @property string $senderName
 * @property Carbon $statusUpdatedAt
 * @property int $supplierId
 * @property string $inspectionCode
 * @property string $pickupCode
 * @property Carbon $deliveryDate
 * @property int $partnerId
 * @property string $employeeCode
 * @property string $supplierCode
 * @property int $senderId
 * @property string $frameNumber
 * @property string $serviceModality
 * @property string $creditNoteFile
 * @property int $statusId
 * @property double $grossAmount
 * @property Carbon $endDate
 * @property Carbon $createdAt
 * @property Carbon $updatedAt
 * @property Status $status
 * @property User $sender
 * @property User $user
 * @property Company $company
 * @property User $supplierUser
 * @property Supplier $supplier
 * @property Portal $portal
 * @property Order $order
 * @property Offer $offer
 * @property Contract $contract
 * @property Audit $audits
 */
class TechnicalService extends PortalModel
{
    use HasStatus, HasGeneratedCode, CamelCaseAttributes, HasFillableRelations;

    const STATUS_OPEN = 25;
    const STATUS_IN_PROCESS = 23;
    const STATUS_SUCCESSFUL = 24;
    const STATUS_READY = 26;
    const STATUS_CANCELLED = 28;
    const STATUS_CONTRACT_CANCELLED = 27;

    const EXPIRED_DAYS = 30;

    const ENTITY = 'technical_service';

    protected $dates = [
        'createdAt',
        'updatedAt',
        'deliveryDate',
        'date',
        'endDate',
        'statusUpdatedAt',
    ];

    protected $fillable = [
        'number',
        'portalId',
        'companyId',
        'productId',
        'userId',
        'productCategoryId',
        'userId',
        'orderId',
        'offerId',
        'contractId',
        'productSize',
        'productColor',
        'productModel',
        'productBrand',
        'employeeCity',
        'employeePhone',
        'employeeEmail',
        'employeeNumber',
        'employeePostalCode',
        'employeeStreet',
        'employeeName',
        'employeeSalutation',
        'supplierCity',
        'supplierEmail',
        'supplierPhone',
        'supplierAdminName',
        'supplierTaxId',
        'supplierBankName',
        'supplierBankAccount',
        'supplierCountry',
        'supplierPostalCode',
        'supplierStreet',
        'supplierName',
        'senderName',
        'supplierId',
        'inspectionCode',
        'pickupCode',
        'deliveryDate',
        'partnerId',
        'employeeCode',
        'supplierCode',
        'senderId',
        'frameNumber',
        'serviceModality',
        'statusId',
        'sender',
        'endDate',
        'statusUpdatedAt',
        'contract',
        'creditNoteFile',
        'grossAmount',
    ];

    protected $fillable_relations = [
        'status',
        'partner',
    ];

    protected $attributes = [
        'status_id' => TechnicalService::STATUS_OPEN,
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function portal(): BelongsTo
    {
        return $this->belongsTo(Portal::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function audits()
    {
        return $this->morphMany(Audit::class, 'model');
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
        return $this->belongsTo(Supplier::class);
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class, 'partner_id');
    }

    public function hasAllContractFields()
    {
        return $this->employeeName && $this->employeePostalCode && $this->employeeCity && $this->employeeStreet
            && $this->employeePhone && $this->employeeSalutation && $this->employeeNumber
            && $this->employeeEmail;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function document()
    {
        return $this->morphOne('App\Documents\Models\Document', 'leasing_document');
    }
}

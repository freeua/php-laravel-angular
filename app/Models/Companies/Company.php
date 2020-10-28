<?php

namespace App\Models\Companies;

use App\Models\City;
use App\Models\LeasingCondition;
use App\Models\Portal;
use App\Models\ProductCategory;
use App\Models\Rates\InsuranceRate;
use App\Models\Rates\ServiceRate;
use App\Models\Status;
use App\Portal\Models\CompanyProductCategory;
use App\Portal\Models\Contract;
use App\Documents\Models\Document;
use App\Portal\Models\Homepage;
use App\Portal\Models\Offer;
use App\Portal\Models\Order;
use App\Portal\Models\Role;
use App\Portal\Models\Supplier;
use App\Portal\Models\User;
use App\Traits\CamelCaseAttributes;
use App\Traits\HasGeneratedCode;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;
use LaravelFillableRelations\Eloquent\Concerns\HasFillableRelations;

/**
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string $slug
 * @property string $color
 * @property string $logo
 * @property string $vat
 * @property string $invoice_type
 * @property string $admin_first_name
 * @property string $admin_last_name
 * @property string $admin_email
 * @property string $zip
 * @property int $city_id
 * @property string $address
 * @property string $phone
 * @property int $max_user_contracts
 * @property float $max_user_amount
 * @property float $min_user_amount
 * @property int $insurance_covered
 * @property string $insurance_covered_type
 * @property float $insurance_covered_amount
 * @property int $maintenance_covered
 * @property int $maintenance_covered_type
 * @property int $maintenance_covered_amount
 * @property boolean $is_accept_employee
 * @property boolean $uses_default_subsidies
 * @property boolean $s_pedelec_disable
 * @property string $gross_conversion
 * @property float $leasing_budget
 * @property float $remaining_leasing_budget
 * @property float $spentLeasingBudget
 * @property int $leasing_rate
 * @property string $leasing_rate_type
 * @property float $leasing_rate_amount
 * @property Status $status
 * @property Carbon $end_contract
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 * @property City $city
 * @property Portal $portal
 * @property Collection $employees
 * @property Collection $suppliers
 * @property Collection $leasingConditions
 * @property Collection insuranceRates
 * @property Collection serviceRates
 * @property Collection $admins
 * @property boolean $manual_contract_approval
 * @property boolean $pecuniary_advantage
 * @property boolean $include_insurance_rate
 * @property boolean $include_service_rate
 * @property string $boni_number
 * @property string $gp_number
 */
class Company extends Model
{
    use SoftDeletes, Notifiable, CamelCaseAttributes, HasFillableRelations, HasGeneratedCode;

    const STATUS_ACTIVE = 5;
    const STATUS_INACTIVE = 6;

    const INVOICE_TYPE_NET = 'net';
    const INVOICE_TYPE_GROSS = 'gross';

    const GROSS_CONVERSION_NETTO = 'netto';
    const GROSS_CONVERSION_BRUTTO = 'brutto';

    const TYPE_FIXED = 'fixed';
    const TYPE_PERCENTAGE = 'percentage';

    const FOLDER_NAME = 'company';

    const ENTITY = 'company';

    /**
     * @var array
     */
    protected $fillable = [
        'logo',
        'color',
        'name',
        'slug',
        'vat',
        'invoice_type',
        'admin_first_name',
        'admin_last_name',
        'admin_email',
        'zip',
        'city_id',
        'address',
        'phone',
        'max_user_contracts',
        'max_user_amount',
        'min_user_amount',
        'override_insurance_amount',
        'insurance_monthly_amount',
        'override_maintenance_amount',
        'insurance_covered',
        'insurance_covered_type',
        'insurance_covered_amount',
        'maintenance_covered',
        'maintenance_covered_type',
        'maintenance_covered_amount',
        'leasing_budget',
        'leasing_rate',
        'leasing_rate_type',
        'leasing_rate_amount',
        'portal_id',
        'status_id',
        'is_accept_employee',
        'uses_default_subsidies',
        'end_contract',
        'gross_conversion',
        'pecuniary_advantage',
        'include_service_rate',
        'include_insurance_rate',
        's_pedelec_disable',
        'boni_number',
        'gp_number'
    ];

    protected $fillable_relations = ['leasingConditions', 'insuranceRates', 'serviceRates', 'status'];

    protected $appends = ['remaining_leasing_budget'];

    protected $dates = ['end_contract'];

    public function suppliers(): BelongsToMany
    {
        return $this->belongsToMany(Supplier::class)->wherePivot('deleted_at', null)->withTimestamps();
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'parent_id');
    }

    public function subcompanies(): HasMany
    {
        return $this->hasMany(Company::class, 'parent_id', 'id');
    }

    public function portal(): BelongsTo
    {
        return $this->belongsTo(Portal::class);
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function leasingConditions(): HasMany
    {
        return $this->hasMany(LeasingCondition::class)
            ->whereHas('productCategory')
            ->orderBy('active_at', 'desc');
    }

    public function insuranceRates(): HasMany
    {
        return $this->hasMany(InsuranceRate::class)->whereHas('productCategory');
    }

    public function serviceRates(): HasMany
    {
        return $this->hasMany(ServiceRate::class)->whereHas('productCategory');
    }

    public function insuranceRatesByProductCategoryId(int $productCategoryId): HasMany
    {
        return $this->hasMany(InsuranceRate::class)
            ->whereHas('productCategory', function (Builder $query) {
                $query->withTrashed();
            })
            ->where('product_category_id', $productCategoryId);
    }

    public function serviceRatesByProductCategoryId(int $productCategoryId): HasMany
    {
        return $this->hasMany(ServiceRate::class)
            ->whereHas('productCategory', function (Builder $query) {
                $query->withTrashed();
            })
            ->where('product_category_id', $productCategoryId);
    }

    public function futureLeasingSettings(): HasMany
    {
        $now = Carbon::now();
        return $this->leasingConditions()
            ->whereDate('active_at', '>', $now)
            ->whereNull('inactive_at');
    }

    public function futureLeasingConditionsByProductCategoryId(int $productCategoryId): HasMany
    {
        return $this->futureLeasingSettings()->where('product_category_id', $productCategoryId);
    }

    public function activeLeasingConditions(): HasMany
    {
        $now = Carbon::now();
        return $this->hasMany(LeasingCondition::class)
            ->whereHas('productCategory', function (Builder $query) {
                $query->withTrashed();
            })
            ->whereDate('active_at', '<=', $now)
            ->where(function (Builder $query) use ($now) {
                return $query->whereDate('inactive_at', '>', $now)
                    ->orWhereNull('inactive_at');
            });
    }

    public function activeLeasingConditionsByProductCategory(ProductCategory $productCategory): HasMany
    {
        return $this->activeLeasingConditions()
            ->where('product_category_id', $productCategory->id);
    }

    public function activeLeasingConditionsByProductCategoryId(int $productCategoryId): HasMany
    {
        return $this->activeLeasingConditions()
            ->where('product_category_id', $productCategoryId);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class)->where('company_id', '=', $this->id)
            ->where('status_id', '=', User::STATUS_ACTIVE);
    }

    public function admins(): HasMany
    {
        return $this->users()
            ->whereHas('roles', function (Builder $query) {
                $query->where('name', Role::ROLE_COMPANY_ADMIN);
            });
    }

    public function employeeAdmins(): HasMany
    {
        return $this->users()
            ->whereHas('roles', function (Builder $query) {
                $query->where('name', Role::ROLE_COMPANY_ADMIN);
            })
            ->whereHas('roles', function (Builder $query) {
                $query->where('name', Role::ROLE_EMPLOYEE);
            });
    }

    public function employees(): HasMany
    {
        return $this->users()->whereHas('roles', function (Builder $query) {
            $query->where('name', Role::ROLE_EMPLOYEE);
        });
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function acceptedOffers(): HasMany
    {
        return $this->offers()->whereIn('status_id', [
            Offer::STATUS_ACCEPTED,
            Offer::STATUS_PENDING_APPROVAL,
            Offer::STATUS_CONTRACT_APPROVED,
        ]);
    }

    public function activeOrders(): HasMany
    {
        return $this->orders()
            ->where(function (Builder $query) {
                $query
                    ->whereDoesntHave('contract')
                    ->orwhereHas('contract', function (Builder $query) {
                        $query
                            ->where('end_date', '>', Carbon::now());
                    });
            });
    }

    public function getRemainingLeasingBudgetAttribute()
    {
        return $this->leasing_budget - ($this->activeOrders()->sum('agreed_purchase_price') / 1.19);
    }

    public function getSpentLeasingBudgetAttribute()
    {
        return $this->activeOrders()->sum('agreed_purchase_price') / 1.19;
    }

    public static function getStatuses(): array
    {
        return [
            self::STATUS_ACTIVE,
            self::STATUS_INACTIVE
        ];
    }

    public static function getInvoiceTypes(): array
    {
        return [
            self::INVOICE_TYPE_NET,
            self::INVOICE_TYPE_GROSS
        ];
    }

    public static function getGrossConversions(): array
    {
        return [
            self::GROSS_CONVERSION_NETTO,
            self::GROSS_CONVERSION_BRUTTO
        ];
    }

    public static function getServicePriceTypes(): array
    {
        return [
            self::TYPE_FIXED,
            self::TYPE_PERCENTAGE
        ];
    }

    public function isActive(): bool
    {
        return $this->status->id === self::STATUS_ACTIVE;
    }

    public function notify(Notification $notification)
    {
        return $this->admins->each(function ($admin) use ($notification) {
            $admin->notify($notification);
        });
    }

    public function homepage()
    {
        return $this->morphOne('App\Portal\Models\Homepage', 'homepageable');
    }

    public function getCompanyHomePage()
    {
        return $this->homepage()->where('type', Homepage::COMPANY_HOMEPAGE)->first();
    }

    public function deleteCompanyHomePage()
    {
        return $this->homepage()->where('type', Homepage::COMPANY_HOMEPAGE)->delete();
    }

    public function getEmployeeHomePage()
    {
        return $this->homepage()->where('type', Homepage::EMPLOYEE_HOMEPAGE)->first();
    }

    public function deleteEmployeeHomePage()
    {
        return $this->homepage()->where('type', Homepage::EMPLOYEE_HOMEPAGE)->delete();
    }

    public function getNotificationStyles(): array
    {
        $result = [];

        if ($this->logo) {
            $result['logo'] = 'storage/' . $this->logo;
        } else {
            $result['logo'] = 'img/logo/company-logo.png';
        }
        $result['color'] = $this->color;
        return $result;
    }

    public function getFrontendFullUrl(string $path = '')
    {
        $domain = "https://{$this->portal->domain}";

        $url = "$domain/firma/{$this->slug}";

        if ($path) {
            $url .= '/' . $path;
        }

        return $url;
    }
}

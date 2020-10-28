<?php

namespace App\Models;

use App\Documents\Models\Document;
use App\Models\Rates\InsuranceRate;
use App\Models\Rates\ServiceRate;
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
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;
use LaravelFillableRelations\Eloquent\Concerns\HasFillableRelations;

/**
 * @property int $id
 * @property string $name
 * @property string $domain
 * @property string $subdomain
 * @property string $logo
 * @property string $color
 * @property string $uuid
 * @property string $application_key
 * @property string $managed_by_database_connection
 * @property string $admin_first_name
 * @property string $admin_last_name
 * @property string $admin_email
 * @property string $company_name
 * @property string $company_zip
 * @property string $leasingablePdf
 * @property string $servicePdf
 * @property string $imprintPdf
 * @property string $imprint
 * @property string $policyPdf
 * @property string $policy
 * @property string $autoresponderText
 * @property boolean $automaticCreditNote
 * @property boolean $allowEmployeeOfferCreation
 * @property string $code
 * @property int $company_city_id
 * @property string $company_address
 * @property string $company_vat
 * @property boolean insurance_rate_subsidy
 * @property string insurance_rate_subsidy_type
 * @property float insurance_rate_subsidy_amount
 * @property boolean service_rate_subsidy
 * @property string service_rate_subsidy_type
 * @property float service_rate_subsidy_amount
 * @property boolean leasing_rate_subsidy
 * @property string leasing_rate_subsidy_type
 * @property float leasing_rate_subsidy_amount
 * @property Status $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 * @property Collection $users
 * @property Collection $admins
 * @property Collection $leasingConditions
 * @property Collection $insuranceRates
 * @property Collection $serviceRates
 */
class Portal extends Model
{
    use Notifiable, HasGeneratedCode, HasFillableRelations, CamelCaseAttributes;
    const STATUS_ACTIVE = 3;

    const STATUS_INACTIVE = 4;

    const ENTITY = 'portal';

    protected $fillable = [
        'name',
        'domain',
        'subdomain',
        'logo',
        'color',
        'adminFirstName',
        'adminLastName',
        'adminEmail',
        'companyName',
        'companyZip',
        'companyCityId',
        'companyAddress',
        'companyVat',
        'leasingablePdf',
        'servicePdf',
        'imprintPdf',
        'imprint',
        'policyPdf',
        'policy',
        'code',
        'insuranceRateSubsidy',
        'insuranceRateSubsidyType',
        'insuranceRateSubsidyAmount',
        'serviceRateSubsidy',
        'serviceRateSubsidyType',
        'serviceRateSubsidyAmount',
        'leasingRateSubsidy',
        'leasingRateSubsidyType',
        'leasingRateSubsidyAmount',
        'autoresponderText',
        'automaticCreditNote',
        'allowEmployeeOfferCreation',
        'partner_id',
    ];

    protected $fillable_relations = [
        'companyCity',
        'status',
        'leasingConditions',
        'serviceRates',
        'insuranceRates'
    ];

    public function companyCity(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function leasingConditions(): HasMany
    {
        return $this->hasMany(LeasingCondition::class)
            ->whereHas('productCategory');
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function suppliers(): BelongsToMany
    {
        return $this->belongsToMany(Supplier::class);
    }

    public function admins(): HasMany
    {
        return $this->users()->whereHas('roles', function (Builder $query) {
            $query->where('name', Role::ROLE_PORTAL_ADMIN);
        });
    }

    public function serviceRates(): HasMany
    {
        return $this->hasMany(ServiceRate::class)
            ->whereHas('productCategory');
    }

    public function defaultServiceRates(): HasMany
    {
        return $this->serviceRates()->where('default', true);
    }

    public function defaultServiceRateByProduct(ProductCategory $productCategory): HasMany
    {
        return $this->defaultServiceRates()->where('product_category_id', $productCategory->id);
    }

    public function insuranceRates(): HasMany
    {
        return $this->hasMany(InsuranceRate::class)
            ->whereHas('productCategory');
    }

    public function defaultInsuranceRates(): HasMany
    {
        return $this->insuranceRates()->where('default', true);
    }

    public function defaultInsuranceRateByProduct(ProductCategory $productCategory): HasMany
    {
        return $this->defaultInsuranceRates()->where('product_category_id', $productCategory->id);
    }

    public function defaultLeasingSettings(): HasMany
    {
        $query = $this->leasingConditions()->where('default', true);
        return $query;
    }

    public function defaultLeasingSettingsByProduct(ProductCategory $productCategory): HasMany
    {
        return $this->defaultLeasingSettings()
            ->where('product_category_id', $productCategory->id);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function isActive(): bool
    {
        return $this->status->id === Portal::STATUS_ACTIVE;
    }

    public function homepage()
    {
        return $this->morphOne('App\Portal\Models\Homepage', 'homepageable');
    }

    public static function getStatuses(): array
    {
        return [
            self::STATUS_ACTIVE,
            self::STATUS_INACTIVE
        ];
    }

    public function notify(Notification $notification)
    {
        return $this->admins->each(function ($admin) use ($notification) {
            $admin->notify($notification);
        });
    }

    public function getNotificationStyles(): array
    {
        $result = [];
        $result['logo'] = 'img/logo/portal-logo.png';
        $result['color'] = '#4D91DF';
        return $result;
    }
}

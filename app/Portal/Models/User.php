<?php

namespace App\Portal\Models;

use App\Contracts\User as UserContract;
use App\Documents\Models\Document;
use App\Helpers\StorageHelper;
use App\Models\Audit;
use App\Models\City;
use App\Models\Companies\Company;
use App\Models\Portal;
use App\Models\Status;
use App\Modules\TechnicalServices\Models\TechnicalService;
use App\Portal\Helpers\AuthHelper;
use App\Portal\Notifications\ResetPassword;
use App\Traits\HasGeneratedCode;
use App\Traits\PasswordAge;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use LaravelFillableRelations\Eloquent\Concerns\HasFillableRelations;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * @property int $id
 * @property string $code
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $street
 * @property int $city_id
 * @property string $postal_code
 * @property string $country
 * @property string $employee_number
 * @property string $salutation
 * @property string $avatar
 * @property string $phone
 * @property string $password
 * @property Carbon $password_updated_at
 * @property Status $status
 * @property string $guard_name
 * @property int $supplier_id
 * @property int $company_id
 * @property int $portal_id
 * @property int $active_contracts
 * @property int $max_user_contracts
 * @property boolean $individual_settings
 * @property boolean $is_accept_offer
 * @property float $max_user_amount
 * @property float $min_user_amount
 * @property float insurance_rate_subsidy
 * @property float insurance_rate_subsidy_type
 * @property float insurance_rate_subsidy_amount
 * @property float service_rate_subsidy
 * @property float service_rate_subsidy_type
 * @property float service_rate_subsidy_amount
 * @property float leasing_rate_subsidy
 * @property float leasing_rate_subsidy_type
 * @property float leasing_rate_subsidy_amount
 * @property string $fullName
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 * @property Supplier $supplier
 * @property Company company
 * @property City city
 * @property Collection $widgets
 * @property Collection $passwordHistories
 * @property Collection $contracts
 * @property Collection $offers
 * @property Collection $permissions
 * @property Collection $acceptedOffers
 * @property Portal $portal
 * @property boolean $policy_checked
 */
class User extends Authenticatable implements JWTSubject, UserContract
{
    use Notifiable, HasGeneratedCode, HasRoles, SoftDeletes, PasswordAge, HasFillableRelations;

    const JWT_APP_KEY = 'apk';

    const STATUS_ACTIVE = 1;

    const STATUS_INACTIVE = 2;

    const STATUS_PENDING = 17;

    const TYPE_FIXED = 'fixed';

    const TYPE_PERCENTAGE = 'percentage';

    const FOLDER_NAME = 'users';

    const ENTITY = 'portal_user';

    /**
     * @var string The name of the table.
     * It exists a System model with table name "suppliers" so we
     * change it to suffixed portal_
     */
    protected $table = 'portal_users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'salutation',
        'first_name',
        'last_name',
        'email',
        'employee_number',
        'country',
        'city_id',
        'postal_code',
        'street',
        'phone',
        'password',
        'password_updated_at',
        'rejected_by',
        'status_id',
        'max_user_contracts',
        'max_user_amount',
        'min_user_amount',
        'insurance_rate_subsidy',
        'insurance_rate_subsidy_type',
        'insurance_rate_subsidy_amount',
        'service_rate_subsidy',
        'service_rate_subsidy_type',
        'service_rate_subsidy_amount',
        'leasing_rate_subsidy',
        'leasing_rate_subsidy_type',
        'leasing_rate_subsidy_amount',
        'individual_settings',
        'policy_checked',
        'portal_id',
        'is_accept_offer'
    ];

    protected $fillable_relations = [
        'city', 'status'
    ];

    public $guard_name = 'api';

    public $system = false;

    /**
     * The attributes that have an accessor should be appended to json
     *
     * @var array
     */
    protected $appends = ['remaining_sign_contracts', 'active_contracts'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password'
    ];
    /**
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'password_updated_at',
    ];

    public function widgets(): HasMany
    {
        return $this->hasMany(Widget::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function contractsCount(): int
    {
        return $this->contracts->count();
    }

    public function contractsTotalPrice(): float
    {
        return $this->contracts->sum('agreed_purchase_price');
    }

    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
    public function technicalServices(): HasMany
    {
        return $this->hasMany(TechnicalService::class);
    }

    public function activeOrders(): HasMany
    {
        return $this->orders()
            ->where(function (Builder $query) {
                $query->whereDoesntHave('contract')
                    ->orWhere(function (Builder $query) {
                        $query
                            ->whereHas('contract', function (Builder $query) {
                                $query
                                    ->where('end_date', '>', Carbon::now());
                            });
                    });
            });
    }

    public function acceptedOffers(): HasMany
    {
        return $this->offers()
            ->where(function (Builder $query) {
                $query->whereIn('status_id', [Offer::STATUS_ACCEPTED, Offer::STATUS_PENDING_APPROVAL])
                    ->orWhere(function (Builder $query) {
                        $query->whereHas('order', function(Builder $query) {
                            $query
                                ->whereDoesntHave('contract');
                        });
                    })
                    ->orWhere(function (Builder $query) {
                        $query->whereHas('order', function(Builder $query) {
                            $query->whereHas('contract', function (Builder $query) {
                                $query
                                    ->where('status_id', '!=', Order::STATUS_CANCELED_CONTRACT)
                                    ->where('end_date', '>', Carbon::now());
                            });
                        });
                    });
            });

    }

    public function canceledContracts(): HasMany
    {
        return $this->contracts()->whereIn('status_id', [Contract::STATUS_CANCELED]);
    }

    public function acceptedOffersTotalPrice(): float
    {
        return $this->acceptedOffers()->sum(\DB::raw('agreed_purchase_price + COALESCE(accessories_discounted_price, 0)'));
    }

    public function passwordHistories(): HasMany
    {
        return $this->hasMany(PasswordHistory::class);
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            self::JWT_APP_KEY => config('app.portal.application_key')
        ];
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function portal(): BelongsTo
    {
        return $this->belongsTo(Portal::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function getRemainingSignContractsAttribute()
    {
        if ($this->company_id) {
            return $this->company->max_user_contracts - $this->acceptedOffers()->count();
        } else {
            return 0;
        }
    }

    public function getActiveContractsAttribute()
    {
        $date = Carbon::today()->toDateString();
        return $this->contracts()
            ->whereDate('start_date', '<', Carbon::today()->toDateString())
            ->whereDate('end_date', '>', Carbon::today()->toDateString())->count();
    }

    public function getMaxBikePriceAttribute()
    {
        if ($this->individual_settings && $this->max_user_amount) {
            return $this->max_user_amount;
        } else {
            return $this->company->max_user_amount;
        }
    }

    public function getMaxNumberContractsAttribute()
    {
        if ($this->individual_settings && $this->max_user_contracts) {
            return $this->max_user_contracts;
        } else {
            return $this->company->max_user_contracts;
        }
    }

    public function getMinBikePriceAttribute()
    {
        if ($this->individual_settings && $this->min_user_amount) {
            return $this->min_user_amount;
        } else {
            return $this->company->min_user_amount;
        }
    }

    public function getRoleName(): ?string
    {
        return join(',', $this->getRoleNames()->toArray());
    }

    public function isActive(): bool
    {
        return $this->status->id === self::STATUS_ACTIVE;
    }

    public function isRole(string $role): bool
    {
        return $this->hasRole($role);
    }

    public function isAdmin(): bool
    {
        return $this->isRole(Role::ROLE_PORTAL_ADMIN);
    }

    public function isCompanyAdmin(): bool
    {
        return $this->isRole(Role::ROLE_COMPANY_ADMIN);
    }

    public function isEmployee(): bool
    {
        return $this->isRole(Role::ROLE_EMPLOYEE);
    }

    public function isSupplier(): bool
    {
        return $this->isRole(Role::ROLE_SUPPLIER_ADMIN);
    }

    public function hasAllContractFields()
    {
        if ($this->first_name && $this->last_name && $this->postal_code && $this->street && $this->phone
            && $this->city_id && $this->salutation && $this->employee_number && $this->email) {
            return true;
        }
        return false;
    }

    public function getFullNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public static function getManageableRoles(): array
    {
        $role = AuthHelper::role();

        switch ($role) {
            case Role::ROLE_PORTAL_ADMIN:
                $roles = [
                    Role::ROLE_PORTAL_ADMIN,
                    Role::ROLE_COMPANY_ADMIN,
                    Role::ROLE_EMPLOYEE,
                ];
                break;
            case Role::ROLE_COMPANY_ADMIN:
                $roles = [
                    Role::ROLE_COMPANY_ADMIN,
                    Role::ROLE_EMPLOYEE,
                ];
                break;
            case Role::ROLE_SUPPLIER_ADMIN:
                $roles = [
                    Role::ROLE_SUPPLIER_ADMIN
                ];
                break;
            default:
                $roles = [];
        }

        return $roles;
    }

    public function getNotificationStyles(): array
    {
        $result = [];

        switch ($this->getRoleNames()->first()) {
            case Role::ROLE_PORTAL_ADMIN:
                if ($this->portal->logo) {
                    $result['logo'] = 'storage/' . $this->portal->logo;
                } else {
                    $result['logo'] = 'img/logo/portal-logo.png';
                }
                $result['color'] = '#4D91DF';
                break;
            case Role::ROLE_COMPANY_ADMIN:
                if ($this->company->logo) {
                    $result['logo'] = 'storage/' . $this->company->logo;
                } else {
                    $result['logo'] = 'img/logo/company-logo.png';
                }
                $result['color'] = $this->company->color;
                break;
            case Role::ROLE_SUPPLIER_ADMIN:
                if ($this->supplier->logo) {
                    $result['logo'] = 'storage/' . $this->supplier->logo;
                } else {
                    $result['logo'] = 'img/logo/supplier-logo.png';
                }
                $result['color'] = '#57E4C2';
                break;
            case Role::ROLE_EMPLOYEE:
                if ($this->company->logo) {
                    $result['logo'] = 'storage/' . $this->company->logo;
                } else {
                    $result['logo'] = 'img/logo/company-logo.png';
                }
                $result['color'] = $this->company->color;
                break;
        }

        return $result;
    }

    public function getModulePath(): ?string
    {
        $path = Role::getRoleModulePath($this->getRoleNames()->first());

        if (is_null($path)) {
            return null;
        }

        return $this->replaceModulePathTags($path);
    }

    public function getLoginPath(): ?string
    {
        $path = $this->getModulePath();

        if ($path && $this->isCompanyAdmin()) {
            $path = str_replace('/admin', '', $path);
        }

        return $path;
    }

    public function replaceModulePathTags(string $path): string
    {
        $tags = $this->getModulePathTags();

        return str_replace(array_keys($tags), array_values($tags), $path);
    }

    public function getModulePathTags(): array
    {
        return [
            '{companySlug}' => $this->company ? $this->company->slug : ''
        ];
    }

    public function getFrontendFullUrl(string $path = null, string $modulePath = null)
    {
        $domain = "https://{$this->portal->domain}";
        if (!$modulePath) {
            $modulePath = $this->getModulePath();
        }

        if (is_null($modulePath)) {
            return null;
        }

        $url = $domain . $modulePath;

        if (!empty($path)) {
            $url .= '/' . $path;
        }

        return $url;
    }

    /**
     * @param bool $private
     *
     * @return string
     */
    public function getDefaultUserFolder(bool $private = false): string
    {
        switch ($this->getRoleNames()->first()) {
            case Role::ROLE_SUPPLIER_ADMIN:
                $folder = Supplier::FOLDER_NAME . DIRECTORY_SEPARATOR . $this->supplier_id;
                break;
            case Role::ROLE_COMPANY_ADMIN:
            case Role::ROLE_EMPLOYEE:
                $folder = Company::FOLDER_NAME . DIRECTORY_SEPARATOR . $this->company_id;
                break;
            default:
                $folder = self::FOLDER_NAME . DIRECTORY_SEPARATOR . $this->id;
                break;
        }

        if ($private) {
            $folder = StorageHelper::PRIVATE_FOLDER . DIRECTORY_SEPARATOR . $folder;
        }

        return $folder;
    }

    public function audits()
    {
        return $this->morphMany(Audit::class, 'model');
    }
}

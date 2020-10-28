<?php

namespace App\Portal\Models;

use App\Models\City;
use App\Models\Companies\Company;
use App\Models\Portal;
use App\Models\Status;
use App\Modules\TechnicalServices\Models\TechnicalService;
use App\Traits\HasGeneratedCode;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;

/**
 * @property int            $id
 * @property string         $code
 * @property int            $portal_id
 * @property int            $original_id
 * @property int            $city_id
 * @property string         $name
 * @property string         $color
 * @property string         $logo
 * @property string         $shop_name
 * @property string         $admin_first_name
 * @property string         $admin_last_name
 * @property string         $admin_email
 * @property string         $phone
 * @property string         $address
 * @property string         $vat
 * @property int            $products_count
 * @property int            $employees_count
 * @property Carbon         $created_at
 * @property Carbon         $updated_at
 * @property Carbon         $deleted_at
 * @property Supplier       $originalSupplier
 * @property Status         $status
 * @property Collection     $portals
 * @property Collection     $companies
 * @property Collection     $users
 * @property City           $city
 * @property string         $cityName
 * @property string         $adminFullName
 * @property string         zip
 * @property string         $gp_number
 * @property string         $bank_account
 * @property string         $grefo
 * @property string         $bank_name
 */
class Supplier extends PortalModel
{
    use SoftDeletes, HasGeneratedCode, Notifiable;

    const STATUS_ACTIVE = 7;

    const STATUS_INACTIVE = 8;

    const WIDGET_ITEMS_COUNT = 5;

    const FOLDER_NAME = 'supplier';

    const ENTITY = 'supplier';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'logo',
        'color',
        'shop_name',
        'admin_first_name',
        'admin_last_name',
        'admin_email',
        'portal_id',
        'original_id',
        'city_id',
        'phone',
        'address',
        'vat',
        'zip',
        'products_count',
        'employees_count',
        'status_id',
        'gp_number',
        'bank_account',
        'bank_name',
        'grefo',
    ];

    /**
     * @return BelongsTo
     */
    public function portals() : BelongsToMany
    {
        return $this->belongsToMany(Portal::class)->withPivot(['status_id', 'id', 'blind_discount']);
    }

    /**
     * @return BelongsTo
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }


    /**
     * @return BelongsTo
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    /**
     * @return HasMany
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * @param bool $withTrash
     *
     * @return BelongsToMany
     */
    public function companies(bool $withTrash = false): BelongsToMany
    {
        $query = $this->belongsToMany(Company::class);

        if (!$withTrash) {
            $query->whereNull('company_supplier.deleted_at');
        }

        return $query
            ->orderBy('name')
            ->withTimestamps();
    }

    /**
     * @return HasMany
     */
    public function settings(): HasMany
    {
        return $this->hasMany(SupplierSetting::class);
    }

    /**
     * @return array
     */
    public static function getStatuses(): array
    {
        return app(Status::class)
            ->newQuery()
            ->where('table', '=', 'suppliers')
            ->get()
            ->transform(function ($status) {
                return $status->id;
            })
            ->toArray();
    }

    /**
     * @return Carbon|null
     */
    public function getActiveFrom(): ?Carbon
    {
        $company = $this->companies(true)->orderBy('company_supplier.created_at')->first();

        return $company ? $company->pivot->created_at : null;
    }

    /**
     * @return array
     */
    public function getNotificationStyles(): array
    {
        $result = [];

        if ($this->logo) {
            $result['logo'] = 'storage/'.$this->logo;
        } else {
            $result['logo'] = 'img/logo/supplier-logo.png';
        }
        $result['color'] = '#57E4C2';

        return $result;
    }

    public function routeNotificationForMail()
    {
        return $this->admin_email;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status_id === self::STATUS_ACTIVE;
    }

    /**
     * @param int $portalId
     * @return mixed
     */
    public function getBlindDiscount(int $portalId)
    {
        return $this->portals()->where('portals.id', '=', $portalId)->first()->pivot->blind_discount;
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
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

    public function homepage()
    {
        return $this->morphOne('App\Portal\Models\Homepage', 'homepageable');
    }

    public function notify(Notification $notification)
    {
        return $this->users->each(function (User $user) use ($notification) {
            $user->notify($notification);
        });
    }
}

<?php

namespace App\System\Models;

use App\Documents\Models\Document;
use App\Models\Status;
use App\Traits\PasswordAge;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Collection;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\System\Notifications\ResetPassword;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Contracts\User as UserContract;

/**
 * @property int        $id
 * @property string     $code
 * @property string     $first_name
 * @property string     $last_name
 * @property string     $email
 * @property string     $password
 * @property Carbon     $password_updated_at
 * @property string     $organization_name
 * @property string     $address
 * @property Carbon     $created_at
 * @property Carbon     $updated_at
 * @property Carbon     $deleted_at
 * @property Status     $status
 * @property Collection $widgets
 * @property Collection $passwordHistories
 */
class User extends Authenticatable implements JWTSubject, UserContract
{
    use Notifiable, SoftDeletes, PasswordAge;

    const CODE_PREFIX = 'SYS';

    const JWT_APP_KEY = 'apk';

    const STATUS_ACTIVE = 1;

    const STATUS_INACTIVE = 2;

    const FOLDER_NAME = 'users';

    const ROLE_ADMIN = 'Systemadministrator';

    const ENTITY = 'system_user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'password_updated_at',
        'organization_name',
        'address',
        'status_id',
    ];
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

    public $system = true;

    /**
     * @return HasMany
     */
    public function widgets(): HasMany
    {
        return $this->hasMany(Widget::class);
    }

    /**
     * @return HasMany
     */
    public function passwordHistories(): HasMany
    {
        return $this->hasMany(PasswordHistory::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            self::JWT_APP_KEY => config('app.system_application_key')
        ];
    }

    /**
     * Send the password reset notification.
     *
     * @param  string $token
     *
     * @return void
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPassword($token));
    }

    /**
     * @return string
     */
    public function getDefaultUserFolder(): string
    {
        return self::FOLDER_NAME . DIRECTORY_SEPARATOR . $this->id;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status === User::STATUS_ACTIVE;
    }

    public function getFullNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }


    public function getNotificationStyles(): array
    {
        $result = [];
        $result['logo'] = 'img/logo/portal-logo.png';
        $result['color'] = '#4D91DF';
        return $result;
    }

    public function getRoleName(): string
    {
        return self::ROLE_ADMIN;
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'uploader');
    }
}

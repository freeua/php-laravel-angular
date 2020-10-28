<?php

namespace App\Portal\Models;

use Carbon\Carbon;

/**
 * @property int    $id
 * @property string $user_id
 * @property string $password
 * @property Carbon $created_at
 */
class PasswordHistory extends PortalModel
{
    /** @var bool */
    public $timestamps = false;
    /** @var array */
    public $dates = ['created_at'];

    protected $table = 'portal_password_histories';
}

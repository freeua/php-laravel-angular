<?php

namespace App\System\Models;

use Carbon\Carbon;

/**
 * Class Setting
 * @package App\System\Models
 * @property int $id
 * @property int $user_id
 * @property string $key
 * @property string $value
 * @property int $active
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Setting extends SystemModel
{
    const CONFIG_KEY = 'settings';
}

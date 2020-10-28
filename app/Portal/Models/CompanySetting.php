<?php

namespace App\Portal\Models;

use Carbon\Carbon;

/**
 * Class CompanySetting
 *
 * @package App\Portal\Models
 * @property int    $id
 * @property int    $company_id
 * @property string $key
 * @property string $value
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class CompanySetting extends PortalModel
{
    protected $table = 'company_settings';
}

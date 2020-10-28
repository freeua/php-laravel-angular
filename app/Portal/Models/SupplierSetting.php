<?php

namespace App\Portal\Models;

use Carbon\Carbon;

/**
 * Class SupplierSetting
 *
 * @package App\Portal\Models
 * @property int    $id
 * @property int    $supplier_id
 * @property string $key
 * @property string $value
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class SupplierSetting extends PortalModel
{
    protected $table = 'supplier_settings';
}

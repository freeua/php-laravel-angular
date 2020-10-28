<?php

namespace App\Portal\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int    $id
 * @property int    $company_id
 * @property int    $supplier_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class CompanySupplier extends PortalModel
{
    use SoftDeletes;
    protected $table = 'company_supplier';
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Rates\InsuranceRate;
use App\Models\Rates\ServiceRate;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use LaravelFillableRelations\Eloquent\Concerns\HasFillableRelations;

/**
 * @property int    $id
 * @property string $name
 * @property Collection $leasingConditions
 * @property Collection $insuranceRates
 * @property Collection $serviceRates
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 */
class ProductCategory extends Model
{
    use SoftDeletes, HasFillableRelations;

    protected $fillable = ['name'];
    protected $fillable_relations = ['leasingConditions', 'serviceRates', 'insuranceRates'];
    protected $table = 'product_categories';


    public function leasingConditions(): HasMany
    {
        return $this->hasMany(LeasingCondition::class)->whereNull('company_id')->whereNull('portal_id');
    }

    public function serviceRates(): HasMany
    {
        return $this->hasMany(ServiceRate::class)->whereNull('company_id')->whereNull('portal_id');
    }

    public function insuranceRates(): HasMany
    {
        return $this->hasMany(InsuranceRate::class)->whereNull('company_id')->whereNull('portal_id');
    }
}

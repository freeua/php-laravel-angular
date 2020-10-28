<?php

namespace App\Models;

use App\Models\Companies\Company;
use App\Traits\CamelCaseAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use LaravelFillableRelations\Eloquent\Concerns\HasFillableRelations;

/**
 * @property int $id
 * @property int $companyId
 * @property int $productCategoryId
 * @property int $factor
 * @property int $period
 * @property int $default
 * @property float $residualValue
 * @property Carbon $activeAt
 * @property Carbon $inactiveAt
 * @property Carbon $createdAt
 * @property Carbon $updatedAt
 * @property Carbon $deletedAt
 * @property Company $company
 * @property Portal $portal
 * @property ProductCategory $productCategory
 */
class LeasingCondition extends Model
{
    use SoftDeletes, CamelCaseAttributes, HasFillableRelations;

    const DEFAULT_INSURANCE = 0.35;

    protected $table = 'leasing_conditions';

    /**
     * @var array
     */
    protected $fillable = [
        'factor',
        'period',
        'residualValue',
        'residual_value',
        'activeAt',
        'active_at',
        'product_category_id',
        'inactiveAt',
        'inactive_at',
    ];

    protected $fillable_relations = [
        'productCategory'
    ];

    protected $dates = [
        'active_at',
        'inactive_at',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function portal(): BelongsTo
    {
        return $this->belongsTo(Portal::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->getTimestamp();
    }

    public function productCategory(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function makeDefault()
    {
        $this->portal->defaultLeasingSettingsByProduct($this->productCategory)->update(['default' => 0]);
        $update = $this->update(['default' => 1]);
        return !!$update;
    }

    public function activate(Carbon $dateActive)
    {
        $this->activeAt = $dateActive;
        $this->inactiveAt = null;
    }

    public function deactivate()
    {
        $this->activeAt = Carbon::today();
        $this->inactiveAt = Carbon::today();
    }

    public function deactivateTomorrow()
    {
        $this->inactiveAt = Carbon::tomorrow();
    }

    public function cancelDeactivation()
    {
        $this->inactiveAt = null;
    }

    public function isActive()
    {
        return $this->activeAt->isPast() && (!$this->inactiveAt || $this->inactiveAt->isFuture());
    }

    public function isFuture()
    {
        return $this->activeAt->isFuture() && $this->inactiveAt === null;
    }
}

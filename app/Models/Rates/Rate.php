<?php
/**
 * Created by PhpStorm.
 * User: jpicornell
 * Date: 2018-12-05
 * Time: 09:52
 */

namespace App\Models\Rates;

use App\Models\Companies\Company;
use App\Models\Portal;
use App\Models\ProductCategory;
use App\Traits\CamelCaseAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LaravelFillableRelations\Eloquent\Concerns\HasFillableRelations;

/**
 * @property int id
 * @property string name
 * @property string amountType
 * @property double amount
 * @property double minimum
 * @property boolean default
 * @property Company company
 * @property Portal portal
 * @property ProductCategory productCategory
 */
abstract class Rate extends Model
{
    const FIXED = 'fixed';
    const PERCENTAGE = 'percentage';
    use CamelCaseAttributes, HasFillableRelations;
    protected $fillable = [
        'name',
        'amountType',
        'amount_type',
        'amount',
        'product_category_id',
        'minimum',
        'default' => 1,
        'type',
        'budget',
    ];
    protected $fillable_relations = [
      'productCategory'
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function portal(): BelongsTo
    {
        return $this->belongsTo(Portal::class);
    }

    public function productCategory(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class);
    }
}

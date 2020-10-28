<?php

namespace App\Portal\Models;

use App\Models\ProductCategory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use LaravelFillableRelations\Eloquent\Concerns\HasFillableRelations;

/**
 * @property int $id
 * @property int $category_id
 * @property int $brand_id
 * @property int $model_id
 * @property int $supplier_id
 * @property string $name
 * @property string $color
 * @property string $size
 * @property string $code
 * @property string $image
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property ProductCategory $category
 * @property ProductImage $images
 * @property ProductBrand $brand
 * @property ProductModel $model
 * @property Supplier $supplier
 */
class Product extends PortalModel
{
    use SoftDeletes, HasFillableRelations;

    protected $fillable = [
        'category_id',
        'supplier_id',
        'color',
        'size',
    ];

    protected $fillable_relations = [
        'brand',
        'model',
    ];

    const ENTITY = 'product';

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(ProductBrand::class);
    }

    public function model(): BelongsTo
    {
        return $this->belongsTo(ProductModel::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }
}

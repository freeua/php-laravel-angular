<?php

namespace App\Portal\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int        $id
 * @property string     $name
 * @property int        $supplier_id
 * @property int        $brand_id
 * @property int        $category_id
 * @property Carbon     $created_at
 * @property Carbon     $updated_at
 * @property Carbon     $deleted_at
 * @property Collection $products
 */
class ProductModel extends PortalModel
{
    use SoftDeletes;

    protected $table = 'product_models';

    protected $fillable = ['name', 'brand_id', 'category_id', 'supplier_id'];

    /**
     * @return HasMany
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * @return HasMany
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(ProductBrand::class);
    }
}

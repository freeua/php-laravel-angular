<?php

namespace App\Portal\Models;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int        $id
 * @property string     $name
 * @property int        $supplier_id
 * @property Carbon     $created_at
 * @property Carbon     $updated_at
 * @property Carbon     $deleted_at
 * @property Collection $products
 */
class ProductBrand extends PortalModel
{
    use SoftDeletes;

    protected $table = 'product_brands';

    protected $fillable = ['name', 'supplier_id'];

    /**
     * @return HasMany
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}

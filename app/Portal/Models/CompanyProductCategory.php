<?php

namespace App\Portal\Models;

use App\Models\Companies\Company;
use App\Models\ProductCategory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyProductCategory extends PortalModel
{
    const ENTITY = 'company_product_categories';

    protected $fillable = [
        'company_id',
        'category_id',
        'status'
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class);
    }
}

<?php

namespace App\Portal\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int        $id
 * @property Company    $company_id
 * @property string     $name
 * @property string     $description
 * @property number     $portal_id
 */
class FaqCategory extends PortalModel
{
    /**
     * @return BelongsTo
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'portal_id',
        'company_id'
    ];
}

<?php

namespace App\Portal\Models;

use App\Models\Companies\Company;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int         $id
 * @property Company     $company_id
 * @property FaqCategory $category_id
 * @property User        $author
 * @property string      $question
 * @property string      $answer
 * @property boolean     $visible
 * @property number      $portal_id
 * @property Carbon      $created_at
 * @property Carbon      $updated_at
 */
class Faq extends PortalModel
{
    /**
     * @return BelongsTo
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(FaqCategory::class);
    }

    /**
     * @var array
     */
    protected $fillable = [
        'category_id',
        'author',
        'company_id',
        'question',
        'answer',
        'visible',
        'portal_id',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'visible' => 'boolean',
    ];
}

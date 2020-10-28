<?php

namespace App\System\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

/**
 * @property int              $id
 * @property int              $user_id
 * @property int              $category_id
 * @property string           $body
 * @property Carbon           $created_at
 * @property Carbon           $updated_at
 * @property FeedbackCategory $category
 */
class Feedback extends SystemModel
{
    protected $table = 'feedbacks';

    /**
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(FeedbackCategory::class);
    }
}

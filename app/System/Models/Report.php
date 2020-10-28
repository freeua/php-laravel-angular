<?php

namespace App\System\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Carbon\Carbon;

/**
 * @property int            $id
 * @property int            $user_id
 * @property string         $body
 * @property Carbon         $created_at
 * @property Carbon         $updated_at
 * @property ReportCategory $categories
 */
class Report extends SystemModel
{
    /**
     * @return BelongsToMany
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(
            ReportCategory::class
        );
    }
}

<?php

namespace App\System\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int    $id
 * @property string $name
 * @property Report $reports
 */
class ReportCategory extends SystemModel
{
    public $timestamps = false;

    /**
     * @return BelongsToMany
     */
    public function reports(): BelongsToMany
    {
        return $this->belongsToMany(
            Report::class
        );
    }
}
